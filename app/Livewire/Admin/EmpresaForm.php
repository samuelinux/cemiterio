<?php

namespace App\Livewire\Admin;

use App\Models\Empresa;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EmpresaForm extends Component
{
    use LivewireAlert;

    public $empresaId;
    public $nome = '';
    public $email = '';
    public $telefone = '';
    public $endereco = '';
    public $cidade = '';
    public $estado = '';
    public $cep = '';
    public $cnpj = '';
    public $ativo = true;

    public $isEditing = false;

    protected $rules = [
        'nome' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'telefone' => 'nullable|string|max:20',
        'endereco' => 'nullable|string',
        'cidade' => 'nullable|string|max:255',
        'estado' => 'nullable|string|max:2',
        'cep' => 'nullable|string|max:10',
        'cnpj' => 'nullable|string|max:18',
        'ativo' => 'boolean',
    ];

    protected $messages = [
        'nome.required' => 'O nome da empresa é obrigatório.',
        'email.email' => 'O email deve ter um formato válido.',
        'estado.max' => 'O estado deve ter no máximo 2 caracteres.',
    ];

    public function mount($empresaId = null)
    {
        if ($empresaId) {
            $this->empresaId = $empresaId;
            $this->isEditing = true;
            $this->loadEmpresa();
        }
    }

    public function loadEmpresa()
    {
        $empresa = Empresa::findOrFail($this->empresaId);
        
        $this->nome = $empresa->nome;
        $this->email = $empresa->email;
        $this->telefone = $empresa->telefone;
        $this->endereco = $empresa->endereco;
        $this->cidade = $empresa->cidade;
        $this->estado = $empresa->estado;
        $this->cep = $empresa->cep;
        $this->cnpj = $empresa->cnpj;
        $this->ativo = $empresa->ativo;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'endereco' => $this->endereco,
            'cidade' => $this->cidade,
            'estado' => $this->estado,
            'cep' => $this->cep,
            'cnpj' => $this->cnpj,
            'ativo' => $this->ativo,
        ];

        if ($this->isEditing) {
            $empresa = Empresa::findOrFail($this->empresaId);
            $empresa->update($data);
            $this->alert('success', 'Empresa atualizada com sucesso!');
        } else {
            Empresa::create($data);
            $this->alert('success', 'Empresa criada com sucesso!');
            $this->reset();
        }
    }

    public function render()
    {
        return view('livewire.admin.empresa-form');
    }
}
