<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Sepultamento;
use App\Models\User;
use Illuminate\Database\Seeder;

class SepultamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = Empresa::where('ativo', true)->get();

        foreach ($empresas as $empresa) {
            // apenas usuários ativos da empresa para atribuir como "criador"
            $users = User::where('empresa_id', $empresa->id)
                ->where('ativo', true)
                ->get();

            if ($users->isEmpty()) {
                // se não houver usuários ativos, pule esta empresa
                continue;
            }

            // Exemplos simples (datas em YYYY-MM-DD)
            $dados = [
                [
                    'nome_falecido'     => 'João Silva Santos',
                    'mae'               => 'Maria Silva',
                    'pai'               => 'José Santos',
                    'indigente'         => false,
                    'natimorto'         => false,
                    'translado'         => false,
                    'membro'            => true,
                    'data_falecimento'  => now()->subDays(2)->format('Y-m-d'),
                    'data_sepultamento' => now()->subDay()->format('Y-m-d'),
                    'quadra'            => 'A',
                    'fila'              => '1',
                    'cova'              => '10',
                    // ano_referencia e numero_sepultamento serão definidos pelo Model (booted)
                    'certidao_obito_path' => null,
                    'observacoes'         => 'Sepultamento realizado no período da tarde.',
                    'ativo'               => true,
                ],
                [
                    'nome_falecido'     => 'Ana Maria Oliveira',
                    'mae'               => 'Cláudia Oliveira',
                    'pai'               => 'Carlos Oliveira',
                    'indigente'         => false,
                    'natimorto'         => false,
                    'translado'         => false,
                    'membro'            => false,
                    'data_falecimento'  => now()->format('Y-m-d'),
                    'data_sepultamento' => now()->addDay()->format('Y-m-d'),
                    'quadra'            => 'B',
                    'fila'              => '2',
                    'cova'              => '45',
                    'certidao_obito_path' => null,
                    'observacoes'         => 'Família solicitou cerimônia religiosa.',
                    'ativo'               => true,
                ],
                [
                    'nome_falecido'     => 'Pedro Costa Lima',
                    'mae'               => 'Helena Costa',
                    'pai'               => 'Antônio Lima',
                    'indigente'         => false,
                    'natimorto'         => false,
                    'translado'         => true,   // exemplo com translado
                    'membro'            => false,
                    'data_falecimento'  => now()->subDays(5)->format('Y-m-d'),
                    'data_sepultamento' => now()->subDays(3)->format('Y-m-d'),
                    'quadra'            => 'C',
                    'fila'              => '3',
                    'cova'              => '78',
                    'certidao_obito_path' => null,
                    'observacoes'         => 'Translado autorizado pela família.',
                    'ativo'               => true,
                ],
            ];

            foreach ($dados as $row) {
                $row['empresa_id'] = $empresa->id;
                $row['user_id']    = $users->random()->id;

                Sepultamento::create($row);
            }

            // (Opcional) exemplo de registro desativado/soft-deleted para testes:
            // $desativado = Sepultamento::create([
            //     'empresa_id'         => $empresa->id,
            //     'user_id'            => $users->random()->id,
            //     'nome_falecido'      => 'Registro Desativado',
            //     'indigente'          => true,
            //     'natimorto'          => false,
            //     'translado'          => false,
            //     'membro'             => false,
            //     'data_falecimento'   => now()->subDays(10)->format('Y-m-d'),
            //     'data_sepultamento'  => now()->subDays(9)->format('Y-m-d'),
            //     'quadra'             => 'D',
            //     'fila'               => '1',
            //     'cova'               => '05',
            //     'observacoes'        => 'Exemplo para testar filtros.',
            //     'ativo'              => false,
            // ]);
            // $desativado->delete(); // soft delete
        }
    }
}
