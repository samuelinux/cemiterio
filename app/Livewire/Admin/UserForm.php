<?php

namespace App\Livewire\Admin;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserForm extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $empresa_id = '';
    public $tipo_usuario = 'user';
    public $ativo = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'empresa_id' => 'nullable|exists:empresas,id',
        'tipo_usuario' => 'required|in:admin,user',
        'ativo' => 'boolean',
    ];

    public function save()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'empresa_id' => $this->empresa_id,
            'tipo_usuario' => $this->tipo_usuario,
            'ativo' => $this->ativo,
        ]);

        $this->dispatch('toast', type: 'success', title: 'Usuário criado com sucesso!');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.admin.user-form', [
            'empresas' => Empresa::orderBy('nome')->get(),
        ]);
    }
}
