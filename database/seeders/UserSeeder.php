<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = Empresa::all();

        foreach ($empresas as $empresa) {
            // Utilizador gestor da empresa
            User::create([
                'name' => "Gestor {$empresa->nome}",
                'email' => "gestor@{$empresa->slug}.com",
                'email_verified_at' => now(),
                'password' => Hash::make('gestor123'),
                'tipo_usuario' => 'user',
                'ativo' => true,
                'empresa_id' => $empresa->id,
            ]);

            // Utilizador funcionário da empresa
            User::create([
                'name' => "Funcionário {$empresa->nome}",
                'email' => "funcionario@{$empresa->slug}.com",
                'email_verified_at' => now(),
                'password' => Hash::make('func123'),
                'tipo_usuario' => 'user',
                'ativo' => true,
                'empresa_id' => $empresa->id,
            ]);

            // Utilizador com acesso limitado
            User::create([
                'name' => "Consultor {$empresa->nome}",
                'email' => "consultor@{$empresa->slug}.com",
                'email_verified_at' => now(),
                'password' => Hash::make('consultor123'),
                'tipo_usuario' => 'user',
                'ativo' => true,
                'empresa_id' => $empresa->id,
            ]);
        }

        // Utilizador inativo para teste
        if ($empresas->first()) {
            User::create([
                'name' => 'Utilizador Inativo',
                'email' => 'inativo@teste.com',
                'email_verified_at' => now(),
                'password' => Hash::make('inativo123'),
                'tipo_usuario' => 'user',
                'ativo' => false,
                'empresa_id' => $empresas->first()->id,
            ]);
        }
    }
}
