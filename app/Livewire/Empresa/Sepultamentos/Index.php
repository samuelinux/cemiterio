<?php

namespace App\Livewire\Empresa\Sepultamentos;

use App\Livewire\Empresa\Sepultamentos\Traits\WithAuditLogs;
use App\Livewire\Empresa\Sepultamentos\Traits\WithSepultamentoCrud;
use App\Livewire\Empresa\Sepultamentos\Traits\WithSepultamentoFilters;
use App\Models\CausaMorte;
use App\Models\Sepultamento;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;
    use WithSepultamentoFilters;
    use WithSepultamentoCrud;
    use WithAuditLogs;

    // -------------------------------------------------
    // Flags de UI (modais)
    // -------------------------------------------------
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showViewModal = false;

    // -------------------------------------------------
    // Controle de permissão
    // -------------------------------------------------
    public bool $canList = true;
    public bool $canCreate = false;
    public bool $canEdit = false;
    public bool $canDelete = false;

    // -------------------------------------------------
    // Formulário
    // -------------------------------------------------
    public ?int $sepultamentoId = null;

    public string $nome_falecido = '';
    public ?string $mae = null;
    public ?string $pai = null;

    public bool $indigente = false;
    public bool $natimorto = false;
    public bool $translado = false;
    public bool $membro = false;

    public ?string $data_falecimento = null;
    public ?string $data_sepultamento = null;

    public ?string $quadra = null;
    public ?string $fila = null;
    public ?string $cova = null;

    public ?string $certidao_obito_path = null;
    public $certidao_obito; // upload
    public ?string $observacoes = null;

    public bool $ativo = true;

    public array $causasSelecionadas = [];
    public array $listaCausas = [];

    // -------------------------------------------------
    // Filtros de busca
    // -------------------------------------------------
    public string $search = '';          // nome_falecido
    public ?string $searchMae = null;
    public ?string $searchPai = null;
    public ?string $searchQuadra = null;
    public ?string $searchFila = null;
    public ?string $searchCova = null;

    public ?string $falecimentoInicio = null;
    public ?string $falecimentoFim = null;
    public ?string $sepultamentoInicio = null;
    public ?string $sepultamentoFim = null;

    public ?string $status = null; // 'ativo', 'inativo', ou null
    public int $perPage = 10;

    // -------------------------------------------------
    // Regras de validação
    // -------------------------------------------------
    protected function rules(): array
    {
        return [
            'nome_falecido' => 'required|string|max:255',
            'mae' => 'nullable|string|max:255',
            'pai' => 'nullable|string|max:255',
            'indigente' => 'boolean',
            'natimorto' => 'boolean',
            'translado' => 'boolean',
            'membro' => 'boolean',
            'data_falecimento' => 'nullable|date|before_or_equal:today',
            'data_sepultamento' => 'nullable|date|after_or_equal:data_falecimento',
            'quadra' => 'nullable|string|max:50',
            'fila' => 'nullable|string|max:50',
            'cova' => 'nullable|string|max:50',
            'certidao_obito' => 'nullable|file|mimes:pdf|max:5120',
            'observacoes' => 'nullable|string|max:1000',
            'ativo' => 'boolean',
            'causasSelecionadas' => 'array',
            'causasSelecionadas.*' => 'integer|exists:causas_morte,id',
        ];
    }

    protected array $messages = [
        'nome_falecido.required' => 'O nome do falecido é obrigatório.',
        'data_sepultamento.after_or_equal' => 'A data de sepultamento deve ser igual ou posterior à data de falecimento.',
        'certidao_obito.mimes' => 'O arquivo deve ser um PDF.',
        'certidao_obito.max' => 'O arquivo não pode exceder 5MB.',
        'causasSelecionadas.*.exists' => 'Uma ou mais causas informadas são inválidas.',
    ];

    // -------------------------------------------------
    // Lifecycle
    // -------------------------------------------------
    public function mount(): void
    {
        $u = Auth::user();

        $this->canList = $u->hasPermissao('sepultamentos', 'consultar');
        $this->canCreate = $u->hasPermissao('sepultamentos', 'cadastrar');
        $this->canEdit = $u->hasPermissao('sepultamentos', 'editar');
        $this->canDelete = $u->hasPermissao('sepultamentos', 'excluir');

        if (!$this->canList) {
            $this->dispatch('toast', type: 'error', title: 'Você não tem permissão para listar sepultamentos.');
        }

        $this->carregarListaCausas();
    }

    // -------------------------------------------------
    // Utilitários
    // -------------------------------------------------
    public function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
    }

    private function resetForm(): void
    {
        $this->reset([
            'sepultamentoId',
            'nome_falecido',
            'mae',
            'pai',
            'indigente',
            'natimorto',
            'translado',
            'membro',
            'data_falecimento',
            'data_sepultamento',
            'quadra',
            'fila',
            'cova',
            'certidao_obito',
            'certidao_obito_path',
            'observacoes',
            'ativo',
            'causasSelecionadas',
        ]);

        $this->ativo = true;
        $this->indigente = $this->natimorto = $this->translado = $this->membro = false;
        $this->causasSelecionadas = [];
    }

    private function preencherFormulario(Sepultamento $s): void
    {
        $this->sepultamentoId = $s->id;
        $this->nome_falecido = (string) $s->nome_falecido;
        $this->mae = $s->mae;
        $this->pai = $s->pai;
        $this->indigente = (bool) $s->indigente;
        $this->natimorto = (bool) $s->natimorto;
        $this->translado = (bool) $s->translado;
        $this->membro = (bool) $s->membro;
        $this->data_falecimento = optional($s->data_falecimento)->format('Y-m-d');
        $this->data_sepultamento = optional($s->data_sepultamento)->format('Y-m-d');
        $this->quadra = $s->quadra;
        $this->fila = $s->fila;
        $this->cova = $s->cova;
        $this->certidao_obito_path = $s->certidao_obito_path;
        $this->observacoes = $s->observacoes;
        $this->ativo = (bool) $s->ativo;

        $this->causasSelecionadas = $s->relationLoaded('causas')
            ? $s->causas->pluck('id')->all()
            : $s->causas()->pluck('causas_morte.id')->all();
    }

    private function carregarListaCausas(): void
    {
        $this->listaCausas = CausaMorte::query()
            ->where('ativo', true)
            ->orderBy('descricao')
            ->get(['id', 'descricao'])
            ->map(fn ($c) => ['id' => $c->id, 'descricao' => $c->descricao])
            ->all();
    }

    // -------------------------------------------------
    // Filtros manuais
    // -------------------------------------------------
    public function aplicarFiltros(): void
    {
        $this->resetPage();
    }

    public function limparFiltros(): void
    {
        $this->reset([
            'search',
            'searchMae',
            'searchPai',
            'searchQuadra',
            'searchFila',
            'searchCova',
            'falecimentoInicio',
            'falecimentoFim',
            'sepultamentoInicio',
            'sepultamentoFim',
            'status',
        ]);
        $this->resetPage();
    }

    // -------------------------------------------------
    // Render
    // -------------------------------------------------
    public function render()
    {
        if (!$this->canList) {
            $sepultamentos = Sepultamento::query()
                ->whereRaw('1=0')
                ->paginate($this->perPage);

            return view('livewire.empresa.sepultamento.index', compact('sepultamentos'));
        }

        $empresaId = Auth::user()->empresa_id;

        $sepultamentos = Sepultamento::query()
            ->where('empresa_id', $empresaId)
            ->when($this->search, fn ($q) => $q->where('nome_falecido', 'like', "%{$this->search}%"))
            ->when($this->searchMae, fn ($q) => $q->where('mae', 'like', "%{$this->searchMae}%"))
            ->when($this->searchPai, fn ($q) => $q->where('pai', 'like', "%{$this->searchPai}%"))
            ->when($this->searchQuadra, fn ($q) => $q->where('quadra', 'like', "%{$this->searchQuadra}%"))
            ->when($this->searchFila, fn ($q) => $q->where('fila', 'like', "%{$this->searchFila}%"))
            ->when($this->searchCova, fn ($q) => $q->where('cova', 'like', "%{$this->searchCova}%"))
            ->when($this->falecimentoInicio, fn ($q) => $q->whereDate('data_falecimento', '>=', $this->falecimentoInicio))
            ->when($this->falecimentoFim, fn ($q) => $q->whereDate('data_falecimento', '<=', $this->falecimentoFim))
            ->when($this->sepultamentoInicio, fn ($q) => $q->whereDate('data_sepultamento', '>=', $this->sepultamentoInicio))
            ->when($this->sepultamentoFim, fn ($q) => $q->whereDate('data_sepultamento', '<=', $this->sepultamentoFim))
            ->when($this->status === 'ativo', fn ($q) => $q->where('ativo', true))
            ->when($this->status === 'inativo', fn ($q) => $q->where('ativo', false))
            ->orderByDesc('data_sepultamento')
            ->paginate($this->perPage);

        return view('livewire.empresa.sepultamento.index', compact('sepultamentos'));
    }
}
