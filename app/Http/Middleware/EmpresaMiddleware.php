<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmpresaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('empresa.login', $request->route('empresa'));
        }

        $user = auth()->user();
        $empresaSlug = $request->route('empresa');

        // Verificar se o utilizador é do tipo 'user' (não admin)
        if (!$user->isUser()) {
            abort(403, 'Acesso negado. Apenas utilizadores de empresa podem acessar esta área.');
        }

        // Verificar se o utilizador pertence à empresa correta
        if (!$user->empresa || $user->empresa->slug !== $empresaSlug) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar esta empresa.');
        }

        // Verificar se o utilizador está ativo
        if (!$user->ativo) {
            abort(403, 'Acesso negado. Sua conta está inativa.');
        }

        return $next($request);
    }
}
