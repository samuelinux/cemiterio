<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Sepultamento;
use App\Models\Empresa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportDadosMigradosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:dados-migrados {--validate-only : Apenas valida os dados sem inserir} {--stop-on-error : Para na primeira falha}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa dados do arquivo dados_migrados_final.json ordenando por data de falecimento/sepultamento';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Iniciando importação dos dados migrados...');

        // Caminho do arquivo JSON
        $jsonPath = base_path('dados_migrados_final.json');

        // Verifica se o arquivo existe
        if (!File::exists($jsonPath)) {
            $this->error("Arquivo não encontrado: {$jsonPath}");
            return 1;
        }

        // Lê e decodifica o JSON
        $jsonData = json_decode(File::get($jsonPath), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Erro ao decodificar o arquivo JSON: ' . json_last_error_msg());
            return 1;
        }

        $this->info("Total de registros encontrados: " . count($jsonData));

        // Validação inicial
        $validatedData = [];
        $errors = [];
        $empresasExistentes = Empresa::pluck('id')->toArray();
        $usuariosExistentes = User::pluck('id')->toArray();

        $this->info('Validando dados...');
        $bar = $this->output->createProgressBar(count($jsonData));
        $bar->start();

        foreach ($jsonData as $index => $item) {
            $bar->advance();
            
            // Valida campos obrigatórios
            if (empty($item['empresa_id']) || empty($item['nome_falecido'])) {
                $errors[] = "Registro #{$index}: Campos obrigatórios ausentes (empresa_id, nome_falecido)";
                continue;
            }

            // Verifica se empresa existe
            if (!in_array($item['empresa_id'], $empresasExistentes)) {
                $errors[] = "Registro #{$index}: Empresa ID {$item['empresa_id']} não encontrada";
                continue;
            }

            // Define user_id como 5 se estiver nulo ou vazio
            if (empty($item['user_id'])) {
                $item['user_id'] = 5;
            }

            // Verifica se usuário existe (se informado)
            if (!empty($item['user_id']) && !in_array($item['user_id'], $usuariosExistentes)) {
                $errors[] = "Registro #{$index}: Usuário ID {$item['user_id']} não encontrado";
                continue;
            }

            // Converte valores booleanos
            $item['indigente'] = (bool) ($item['indigente'] ?? false);
            $item['natimorto'] = (bool) ($item['natimorto'] ?? false);
            $item['translado'] = (bool) ($item['translado'] ?? false);
            $item['membro'] = (bool) ($item['membro'] ?? false);
            $item['ativo'] = (bool) ($item['ativo'] ?? true);

            // Verifica formato das datas
            if (!empty($item['data_falecimento']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $item['data_falecimento'])) {
                $errors[] = "Registro #{$index}: Formato inválido para data_falecimento";
                continue;
            }

            if (!empty($item['data_sepultamento']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $item['data_sepultamento'])) {
                $errors[] = "Registro #{$index}: Formato inválido para data_sepultamento";
                continue;
            }

            $validatedData[] = $item;
        }

        $bar->finish();
        $this->newLine();

        // Exibe erros encontrados
        if (!empty($errors)) {
            $this->warn("Erros encontrados durante a validação:");
            foreach (array_slice($errors, 0, 10) as $error) { // Mostra apenas os primeiros 10
                $this->warn("- {$error}");
            }
            
            if (count($errors) > 10) {
                $this->warn("... e mais " . (count($errors) - 10) . " erros.");
            }

            if (!$this->confirm('Deseja continuar mesmo com os erros?')) {
                return 1;
            }
        }

        // Se for apenas validação, termina aqui
        if ($this->option('validate-only')) {
            $this->info('Validação concluída. Nenhum dado foi inserido.');
            return 0;
        }

        // Ordena os dados por data de falecimento ou data de sepultamento
        usort($validatedData, function ($a, $b) {
            // Usa data de sepultamento se disponível, senão usa data de falecimento
            $dateA = !empty($a['data_sepultamento']) ? $a['data_sepultamento'] : $a['data_falecimento'];
            $dateB = !empty($b['data_sepultamento']) ? $b['data_sepultamento'] : $b['data_falecimento'];
            
            // Se ambas forem nulas, mantém a ordem original
            if (empty($dateA) && empty($dateB)) {
                return 0;
            }
            
            // Se uma for nula, coloca ela depois
            if (empty($dateA)) {
                return 1;
            }
            
            if (empty($dateB)) {
                return -1;
            }
            
            // Compara as datas
            return strcmp($dateA, $dateB);
        });

        $this->info('Dados ordenados por data de falecimento/sepultamento.');

        // Confirmação antes de inserir
        if (!$this->confirm('Deseja realmente inserir ' . count($validatedData) . ' registros no banco de dados na ordem cronológica?')) {
            $this->info('Operação cancelada.');
            return 0;
        }

        // Inserção dos dados
        $this->info('Inserindo dados na ordem cronológica...');
        $bar = $this->output->createProgressBar(count($validatedData));
        $bar->start();

        $successCount = 0;
        $failCount = 0;
        $stopOnError = $this->option('stop-on-error');

        foreach ($validatedData as $index => $item) {
            $bar->advance();
            
            try {
                // Remove campos que não devem ser inseridos diretamente
                unset($item['causas']); // Se houver relacionamentos de causas, eles precisam ser tratados separadamente
                
                // Criar o registro - o modelo vai gerar automaticamente ano_referencia e numero_sepultamento
                Sepultamento::create($item);
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                $this->error("Erro ao inserir registro #{$index}: " . $e->getMessage());
                
                // Se a opção --stop-on-error estiver ativa, para a execução imediatamente
                if ($stopOnError) {
                    $this->error("Execução interrompida devido a erro no primeiro registro. Registros processados: {$successCount} sucesso(s), {$failCount} falha(s).");
                    $bar->finish();
                    $this->newLine();
                    return 1;
                }
            }
        }

        $bar->finish();
        $this->newLine();

        $this->info("Importação concluída!");
        $this->info("Registros inseridos com sucesso: {$successCount}");
        $this->info("Registros com falha: {$failCount}");

        return 0;
    }
}