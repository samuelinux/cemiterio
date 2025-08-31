<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $tabela
     * @param  string  $acao
     */
    public function handle(Request $request, Closure $next, string $tabela, string $acao): Response
    {
        $user = Auth::user();
        
        // Admins têm acesso total
        if ($user->isAdmin()) {
            return $next($request);
        }
        
        // Verificar se o utilizador tem a permissão específica
        $permissao = $user->permissoes()
            ->where('tabela', $tabela)
            ->first();
            
        if (!$permissao || !$permissao->$acao) {
            abort(403, 'Não tem permissão para realizar esta ação.');
        }

        return $next($request);
    }
}
