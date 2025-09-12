<?php

namespace App\Http\Middleware;

use App\Models\Empresa;
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
        // Não logado → 403 (como você preferiu)
        if (! auth()->check()) {
            abort(403, 'Acesso negado. É necessário estar autenticado.');
        }

        $user = auth()->user();

        // Pega o parâmetro {empresa} da rota (pode ser slug string ou Model Empresa)
        $param = $request->route('empresa');
        $empresa = $param instanceof Empresa
            ? $param
            : Empresa::where('slug', (string) $param)->first();

        if (! $empresa) {
            abort(404, 'Empresa não encontrada.');
        }

        // Somente usuários do tipo "user" entram na área da empresa
        if (! $user->isUser()) {
            abort(403, 'Acesso negado. Apenas utilizadores de empresa podem acessar esta área.');
        }

        // Usuário precisa pertencer à empresa da rota
        if ((int) $user->empresa_id !== (int) $empresa->id) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar esta empresa.');
        }

        // Conta/empresa ativas
        if (! $user->ativo || ! $empresa->ativo) {
            abort(403, 'Acesso negado. Conta ou empresa inativa.');
        }

        return $next($request);
    }
}
