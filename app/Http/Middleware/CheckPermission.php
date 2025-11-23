<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, \Closure $next, string $tabela, string $acao): Response
    {
        // Se não houver usuário autenticado → erro
        if (!Auth::check()) {
            abort(403, 'Acesso negado. É necessário estar autenticado.');
        }

        $user = Auth::user();

        // Admins têm acesso total
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Ações válidas
        $acoesValidas = ['consultar', 'cadastrar', 'editar', 'excluir'];
        if (!in_array($acao, $acoesValidas, true)) {
            abort(403, 'Ação inválida.');
        }

        // Verificar permissão na tabela
        $permissao = $user->permissoes()->where('tabela', $tabela)->first();

        if (!$permissao || !$permissao->$acao) {
            abort(403, 'Você não tem permissão para realizar esta ação.');
        }

        return $next($request);
    }
}
