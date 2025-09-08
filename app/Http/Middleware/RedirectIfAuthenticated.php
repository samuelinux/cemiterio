<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, \Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Se está em rotas admin
                if ($request->routeIs('admin.*')) {
                    return redirect()->route('admin.dashboard');
                }

                // Se está em rotas de empresa
                if ($request->routeIs('empresa.*') && $request->route('empresa')) {
                    return redirect()->route('empresa.dashboard', $request->route('empresa'));
                }

                // Fallback: admin
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}
