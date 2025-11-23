<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Empresa::create([
            'nome' => 'Cemitério São João',
            'email' => 'contato@cemiteriosaojoao.com',
            'telefone' => '(11) 1234-5678',
            'endereco' => 'Rua das Flores, 123',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
            'cep' => '01234-567',
            'cnpj' => '12.345.678/0001-90',
            'ativo' => true,
        ]);

        Empresa::create([
            'nome' => 'Cemitério Paz Eterna',
            'email' => 'admin@pazeterna.com',
            'telefone' => '(21) 9876-5432',
            'endereco' => 'Avenida Central, 456',
            'cidade' => 'Rio de Janeiro',
            'estado' => 'RJ',
            'cep' => '20123-456',
            'cnpj' => '98.765.432/0001-10',
            'ativo' => true,
        ]);

        Empresa::create([
            'nome' => 'Cemitério Vale Verde',
            'email' => 'contato@valeverde.com',
            'telefone' => '(31) 5555-1234',
            'endereco' => 'Rua do Vale, 789',
            'cidade' => 'Belo Horizonte',
            'estado' => 'MG',
            'cep' => '30123-789',
            'cnpj' => '11.222.333/0001-44',
            'ativo' => false, // Empresa inativa para teste
        ]);
    }
}
