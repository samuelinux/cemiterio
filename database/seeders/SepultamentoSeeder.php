<?php

namespace Database\Seeders;

use App\Models\Sepultamento;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            $users = User::where('empresa_id', $empresa->id)
                         ->where('ativo', true)
                         ->get();

            if ($users->isEmpty()) continue;

            // Criar sepultamentos de exemplo
            $sepultamentos = [
                [
                    'nome_falecido' => 'João Silva Santos',
                    'cpf_falecido' => '123.456.789-00',
                    'data_nascimento' => '1945-03-15',
                    'data_falecimento' => now()->subDays(2)->format('Y-m-d'),
                    'causa_morte' => 'Causas naturais',
                    'naturalidade' => 'São Paulo, SP',
                    'profissao' => 'Aposentado',
                    'estado_civil' => 'casado',
                    'sexo' => 'masculino',
                    'data_sepultamento' => now()->subDay()->format('Y-m-d'),
                    'hora_sepultamento' => '14:00',
                    'local_sepultamento' => 'Cemitério Central',
                    'quadra' => 'A',
                    'gaveta' => '1',
                    'numero_sepultura' => 'A-001',
                    'tipo_sepultamento' => 'inumacao',
                    'nome_responsavel' => 'Maria Silva Santos',
                    'cpf_responsavel' => '987.654.321-00',
                    'telefone_responsavel' => '(11) 99999-1234',
                    'parentesco' => 'Esposa',
                    'numero_certidao_obito' => 'CO-2024-001',
                    'cartorio_certidao' => '1º Cartório de Registro Civil',
                    'numero_declaracao_obito' => 'DO-2024-001',
                    'observacoes' => 'Sepultamento realizado conforme tradição familiar.',
                ],
                [
                    'nome_falecido' => 'Ana Maria Oliveira',
                    'cpf_falecido' => '456.789.123-00',
                    'data_nascimento' => '1960-07-22',
                    'data_falecimento' => now()->format('Y-m-d'),
                    'causa_morte' => 'Complicações cardíacas',
                    'naturalidade' => 'Rio de Janeiro, RJ',
                    'profissao' => 'Professora',
                    'estado_civil' => 'viuvo',
                    'sexo' => 'feminino',
                    'data_sepultamento' => now()->addDay()->format('Y-m-d'),
                    'hora_sepultamento' => '10:00',
                    'local_sepultamento' => 'Cemitério Municipal',
                    'quadra' => 'B',
                    'gaveta' => '2',
                    'numero_sepultura' => 'B-045',
                    'tipo_sepultamento' => 'inumacao',
                    'nome_responsavel' => 'Carlos Oliveira Filho',
                    'cpf_responsavel' => '321.654.987-00',
                    'telefone_responsavel' => '(21) 88888-5678',
                    'parentesco' => 'Filho',
                    'numero_certidao_obito' => 'CO-2024-002',
                    'cartorio_certidao' => '2º Cartório de Registro Civil',
                    'numero_declaracao_obito' => 'DO-2024-002',
                    'observacoes' => 'Família solicitou cerimônia religiosa.',
                ],
                [
                    'nome_falecido' => 'Pedro Costa Lima',
                    'cpf_falecido' => '789.123.456-00',
                    'data_nascimento' => '1938-12-10',
                    'data_falecimento' => now()->subDays(5)->format('Y-m-d'),
                    'causa_morte' => 'Idade avançada',
                    'naturalidade' => 'Belo Horizonte, MG',
                    'profissao' => 'Comerciante',
                    'estado_civil' => 'solteiro',
                    'sexo' => 'masculino',
                    'data_sepultamento' => now()->subDays(3)->format('Y-m-d'),
                    'hora_sepultamento' => '16:30',
                    'local_sepultamento' => 'Cemitério da Saudade',
                    'quadra' => 'C',
                    'gaveta' => '3',
                    'numero_sepultura' => 'C-078',
                    'tipo_sepultamento' => 'cremacao',
                    'nome_responsavel' => 'José Costa Lima',
                    'cpf_responsavel' => '654.321.987-00',
                    'telefone_responsavel' => '(31) 77777-9012',
                    'parentesco' => 'Irmão',
                    'numero_certidao_obito' => 'CO-2024-003',
                    'cartorio_certidao' => '3º Cartório de Registro Civil',
                    'numero_declaracao_obito' => 'DO-2024-003',
                    'observacoes' => 'Cremação conforme vontade expressa do falecido.',
                ],
            ];

            foreach ($sepultamentos as $sepultamentoData) {
                $sepultamentoData['empresa_id'] = $empresa->id;
                $sepultamentoData['user_id'] = $users->random()->id;
                
                Sepultamento::create($sepultamentoData);
            }
        }
    }
}
