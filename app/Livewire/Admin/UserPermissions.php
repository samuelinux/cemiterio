<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Permissao;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UserPermissions extends Component
{
    use LivewireAlert;

    public $userId;
    public $user;
    public $permissoes = [];
    
    // Tabelas disponíveis no sistema
    public $tabelas = [
        'sepultamentos' => 'Sepultamentos',
        'empresas' => 'Empresas',
        'users' => 'Utilizadores',
    ];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::with(['empresa', 'permissoes'])->findOrFail($userId);
        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        // Inicializar array de permissões
        foreach ($this->tabelas as $tabela => $nome) {
            $this->permissoes[$tabela] = [
                'consultar' => false,
                'cadastrar' => false,
                'editar' => false,
                'excluir' => false,
            ];
        }

        // Carregar permissões existentes
        foreach ($this->user->permissoes as $permissao) {
            if (isset($this->permissoes[$permissao->tabela])) {
                $this->permissoes[$permissao->tabela] = [
                    'consultar' => $permissao->consultar,
                    'cadastrar' => $permissao->cadastrar,
                    'editar' => $permissao->editar,
                    'excluir' => $permissao->excluir,
                ];
            }
        }
    }

    public function updatePermission($tabela, $acao, $valor)
    {
        $this->permissoes[$tabela][$acao] = $valor;
    }

    public function savePermissions()
    {
        // Remover permissões existentes
        $this->user->permissoes()->delete();

        // Criar novas permissões
        foreach ($this->permissoes as $tabela => $acoes) {
            // Só criar se pelo menos uma ação estiver ativa
            if (array_filter($acoes)) {
                Permissao::create([
                    'user_id' => $this->userId,
                    'tabela' => $tabela,
                    'consultar' => $acoes['consultar'],
                    'cadastrar' => $acoes['cadastrar'],
                    'editar' => $acoes['editar'],
                    'excluir' => $acoes['excluir'],
                ]);
            }
        }

        $this->alert('success', 'Permissões atualizadas com sucesso!');
    }

    public function grantAllPermissions($tabela)
    {
        $this->permissoes[$tabela] = [
            'consultar' => true,
            'cadastrar' => true,
            'editar' => true,
            'excluir' => true,
        ];
    }

    public function revokeAllPermissions($tabela)
    {
        $this->permissoes[$tabela] = [
            'consultar' => false,
            'cadastrar' => false,
            'editar' => false,
            'excluir' => false,
        ];
    }

    public function render()
    {
        return view('livewire.admin.user-permissions');
    }
}
