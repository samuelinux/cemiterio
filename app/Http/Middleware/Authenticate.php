<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        if ($request->is('admin/*')) {
            return route('admin.login');
        }

        // Se for rota de empresa → manda para empresa.login (slug dinâmico)
        if ($request->route('empresa')) {
            return route('empresa.login', [
                'empresa' => $request->route('empresa'),
            ]);
        }

        // Se não caiu em nenhum dos casos → rota inválida → erro 404
        throw new NotFoundHttpException();
    }
}
