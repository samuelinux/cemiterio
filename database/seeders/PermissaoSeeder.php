<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Permissao;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('tipo_usuario', 'user')->get();

        foreach ($users as $user) {
            // Gestores têm permissões completas
            if (str_contains($user->name, 'Gestor')) {
                $this->createFullPermissions($user->id);
            }
            // Funcionários têm permissões de sepultamentos
            elseif (str_contains($user->name, 'Funcionário')) {
                $this->createSepultamentoPermissions($user->id);
            }
            // Consultores só podem consultar
            elseif (str_contains($user->name, 'Consultor')) {
                $this->createReadOnlyPermissions($user->id);
            }
        }
    }

    private function createFullPermissions($userId)
    {
        $tabelas = ['sepultamentos', 'empresas', 'users'];
        
        foreach ($tabelas as $tabela) {
            Permissao::create([
                'user_id' => $userId,
                'tabela' => $tabela,
                'consultar' => true,
                'cadastrar' => true,
                'editar' => true,
                'excluir' => true,
            ]);
        }
    }

    private function createSepultamentoPermissions($userId)
    {
        Permissao::create([
            'user_id' => $userId,
            'tabela' => 'sepultamentos',
            'consultar' => true,
            'cadastrar' => true,
            'editar' => true,
            'excluir' => false, // Funcionários não podem excluir
        ]);
    }

    private function createReadOnlyPermissions($userId)
    {
        $tabelas = ['sepultamentos', 'empresas', 'users'];
        
        foreach ($tabelas as $tabela) {
            Permissao::create([
                'user_id' => $userId,
                'tabela' => $tabela,
                'consultar' => true,
                'cadastrar' => false,
                'editar' => false,
                'excluir' => false,
            ]);
        }
    }
}
