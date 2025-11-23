<?php

// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        // Pode ter uma listagem com Livewire, se já tiver, a view pode chamar @livewire('admin.user-index')
        return view('admin.users.index');
    }

    public function create()
    {
        // Se você usa Livewire para criação, retorne a view com o componente de criação
        return view('admin.users.create');
    }

    public function store()
    {
        abort(405);
    } // usando Livewire para salvar

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Página principal de edição: dados + permissões (dois cards)
        return view('admin.users.edit', compact('user'));
    }

    public function update()
    {
        abort(405);
    } // usando Livewire para salvar

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuário excluído.');
    }

    // Rotas extras já existentes no seu routes/web.php:
    public function permissions(User $user)
    {
        // Página específica só de permissões (se desejar usar)
        return view('admin.users.permissions', compact('user'));
    }

    public function updatePermissions()
    {
        abort(405);
    } // Livewire salva
}
