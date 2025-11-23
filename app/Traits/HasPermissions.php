<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasPermissions
{
    /**
     * Verifica se o utilizador tem permissão para uma ação específica numa tabela
     */
    public function hasPermission(string $tabela, string $acao): bool
    {
        $user = Auth::user();
        
        // Admins têm acesso total
        if ($user->isAdmin()) {
            return true;
        }
        
        // Verificar permissão específica
        $permissao = $user->permissoes()
            ->where('tabela', $tabela)
            ->first();
            
        return $permissao && $permissao->$acao;
    }

    /**
     * Verifica se pode consultar uma tabela
     */
    public function canView(string $tabela): bool
    {
        return $this->hasPermission($tabela, 'consultar');
    }

    /**
     * Verifica se pode criar registos numa tabela
     */
    public function canCreate(string $tabela): bool
    {
        return $this->hasPermission($tabela, 'cadastrar');
    }

    /**
     * Verifica se pode editar registos numa tabela
     */
    public function canEdit(string $tabela): bool
    {
        return $this->hasPermission($tabela, 'editar');
    }

    /**
     * Verifica se pode excluir registos numa tabela
     */
    public function canDelete(string $tabela): bool
    {
        return $this->hasPermission($tabela, 'excluir');
    }

    /**
     * Lança exceção se não tiver permissão
     */
    public function checkPermission(string $tabela, string $acao): void
    {
        if (!$this->hasPermission($tabela, $acao)) {
            abort(403, 'Não tem permissão para realizar esta ação.');
        }
    }
}

