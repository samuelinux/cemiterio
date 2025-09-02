<?php

namespace App\Livewire\Empresa;

use App\Models\Sepultamento;
use App\Traits\HasPermissions;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SepultamentoForm extends Component
{
    use HasPermissions;

    public $sepultamentoId;

    // Dados do falecido
    public $nome_falecido = '';
    public $cpf_falecido = '';
    public $data_nascimento = '';
    public $data_falecimento = '';
    public $causa_morte = '';
    public $naturalidade = '';
    public $profissao = '';
    public $estado_civil = '';
    public $sexo = '';

    // Dados do sepultamento
    public $data_sepultamento = '';
    public $hora_sepultamento = '';
    public $local_sepultamento = '';
    public $quadra = '';
    public $gaveta = '';
    public $numero_sepultura = '';
    public $tipo_sepultamento = 'inumacao';

    // Dados do responsável
    public $nome_responsavel = '';
    public $cpf_responsavel = '';
    public $telefone_responsavel = '';
    public $parentesco = '';

    // Documentação
    public $numero_certidao_obito = '';
    public $cartorio_certidao = '';
    public $numero_declaracao_obito = '';

    // Observações
    public $observacoes = '';

    public $isEditing = false;

    protected $rules = [
        'nome_falecido' => 'required|string|max:255',
        'cpf_falecido' => 'nullable|string|max:14',
        'data_nascimento' => 'nullable|date',
        'data_falecimento' => 'required|date',
        'causa_morte' => 'nullable|string|max:255',
        'naturalidade' => 'nullable|string|max:255',
        'profissao' => 'nullable|string|max:255',
        'estado_civil' => 'nullable|in:solteiro,casado,divorciado,viuvo',
        'sexo' => 'nullable|in:masculino,feminino',
        'data_sepultamento' => 'required|date',
        'hora_sepultamento' => 'nullable|date_format:H:i',
        'local_sepultamento' => 'required|string|max:255',
        'quadra' => 'nullable|string|max:50',
        'gaveta' => 'nullable|string|max:50',
        'numero_sepultura' => 'nullable|string|max:50',
        'tipo_sepultamento' => 'required|in:inumacao,cremacao',
        'nome_responsavel' => 'required|string|max:255',
        'cpf_responsavel' => 'nullable|string|max:14',
        'telefone_responsavel' => 'nullable|string|max:20',
        'parentesco' => 'nullable|string|max:100',
        'numero_certidao_obito' => 'nullable|string|max:100',
        'cartorio_certidao' => 'nullable|string|max:255',
        'numero_declaracao_obito' => 'nullable|string|max:100',
        'observacoes' => 'nullable|string',
    ];

    protected $messages = [
        'nome_falecido.required' => 'O nome do falecido é obrigatório.',
        'data_falecimento.required' => 'A data de falecimento é obrigatória.',
        'data_sepultamento.required' => 'A data de sepultamento é obrigatória.',
        'local_sepultamento.required' => 'O local de sepultamento é obrigatório.',
        'nome_responsavel.required' => 'O nome do responsável é obrigatório.',
        'tipo_sepultamento.required' => 'O tipo de sepultamento é obrigatório.',
    ];

    public function mount($sepultamentoId = null)
    {
        if ($sepultamentoId) {
            $this->sepultamentoId = $sepultamentoId;
            $this->isEditing = true;
            $this->checkPermission('sepultamentos', 'editar');
            $this->loadSepultamento();
        } else {
            $this->checkPermission('sepultamentos', 'cadastrar');
        }

        // Definir data padrão como hoje
        if (!$this->data_sepultamento) {
            $this->data_sepultamento = now()->format('Y-m-d');
        }
    }

    public function loadSepultamento()
    {
        $sepultamento = Sepultamento::where('empresa_id', Auth::user()->empresa_id)
            ->findOrFail($this->sepultamentoId);

        // Carregar todos os campos
        foreach ($this->rules as $field => $rule) {
            if (property_exists($this, $field)) {
                $this->$field = $sepultamento->$field;
            }
        }

        // Formatar datas
        $this->data_nascimento = $sepultamento->data_nascimento?->format('Y-m-d');
        $this->data_falecimento = $sepultamento->data_falecimento?->format('Y-m-d');
        $this->data_sepultamento = $sepultamento->data_sepultamento?->format('Y-m-d');
        $this->hora_sepultamento = $sepultamento->hora_sepultamento?->format('H:i');
    }

    public function save()
    {
        $this->validate();

        $data = [];
        foreach ($this->rules as $field => $rule) {
            if (property_exists($this, $field)) {
                $data[$field] = $this->$field;
            }
        }

        $data['empresa_id'] = Auth::user()->empresa_id;
        $data['user_id'] = Auth::id();

        if ($this->isEditing) {
            $sepultamento = Sepultamento::where('empresa_id', Auth::user()->empresa_id)
                ->findOrFail($this->sepultamentoId);
            $sepultamento->update($data);
            $this->alert('success', 'Sepultamento atualizado com sucesso!');
        } else {
            Sepultamento::create($data);
            $this->alert('success', 'Sepultamento registado com sucesso!');
            $this->reset();
            $this->data_sepultamento = now()->format('Y-m-d');
        }
    }

    public function render()
    {
        return view('livewire.empresa.sepultamento-form');
    }
}
