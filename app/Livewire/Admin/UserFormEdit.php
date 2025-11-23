<?php

// app/Livewire/Admin/UserFormEdit.php

namespace App\Livewire\Admin;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserFormEdit extends Component
{
    public int $userId;

    public string $name = '';
    public string $email = '';
    public ?string $password = null; // opcional
    public ?int $empresa_id = null;
    public string $tipo_usuario = 'user';
    public bool $ativo = true;

    public function mount(int $userId): void
    {
        $this->userId = $userId;

        $u = User::findOrFail($userId);
        $this->name = $u->name;
        $this->email = $u->email;
        $this->empresa_id = $u->empresa_id;
        $this->tipo_usuario = $u->tipo_usuario ?: 'user';
        $this->ativo = (bool) $u->ativo;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => ['nullable', 'min:6'],
            'empresa_id' => ['nullable', 'exists:empresas,id'],
            'tipo_usuario' => ['required', 'in:admin,user'],
            'ativo' => ['boolean'],
        ];
    }

    protected array $messages = [
        'name.required' => 'O nome é obrigatório.',
        'email.required' => 'O e-mail é obrigatório.',
        'email.email' => 'Informe um e-mail válido.',
        'email.unique' => 'Este e-mail já está em uso.',
        'password.min' => 'A senha deve ter ao menos 6 caracteres.',
        'empresa_id.exists' => 'Empresa inválida.',
        'tipo_usuario.in' => 'Tipo de usuário inválido.',
    ];

    public function save(): void
    {
        $data = $this->validate();

        $u = User::findOrFail($this->userId);

        $update = [
            'name' => $this->name,
            'email' => $this->email,
            'empresa_id' => $this->empresa_id,
            'tipo_usuario' => $this->tipo_usuario,
            'ativo' => $this->ativo,
        ];

        if (filled($this->password)) {
            $update['password'] = $this->password; // cast 'hashed' no Model fará o hash
        }

        $u->update($update);

        $this->dispatch('toast', type: 'success', title: 'Usuário atualizado!');
    }

    public function render()
    {
        return view('livewire.admin.user-form-edit', [
            'empresas' => Empresa::orderBy('nome')->get(['id', 'nome']),
        ]);
    }
}
