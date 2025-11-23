<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EmpresaLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm($empresaSlug)
    {
        $empresa = Empresa::where('slug', $empresaSlug)->where('ativo', true)->firstOrFail();
        return view('auth.empresa.login', compact('empresa'));
    }

    public function login(Request $request, $empresaSlug)
    {
        $empresa = Empresa::where('slug', $empresaSlug)->where('ativo', true)->firstOrFail();
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['tipo_usuario'] = 'user';
        $credentials['ativo'] = true;
        $credentials['empresa_id'] = $empresa->id;

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('empresa.dashboard', $empresaSlug));
        }

        throw ValidationException::withMessages([
            'email' => ['As credenciais fornecidas não correspondem aos nossos registos ou você não tem acesso a esta empresa.'],
        ]);
    }

    public function logout(Request $request, $empresaSlug)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('empresa.login', $empresaSlug);
    }
}
