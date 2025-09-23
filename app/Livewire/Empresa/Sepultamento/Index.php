<?php

namespace App\Livewire\Empresa\Sepultamento;

use App\Livewire\Empresa\Sepultamento\Traits\WithAuditLogs;
use App\Livewire\Empresa\Sepultamento\Traits\WithSepultamentoCrud;
use App\Livewire\Empresa\Sepultamento\Traits\WithSepultamentoExport;
use App\Livewire\Empresa\Sepultamento\Traits\WithSepultamentoFilters;
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
    use WithSepultamentoExport;

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
    // Formulário (CRUD)
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

    // -------------------------------------------------
    // Relação (causas da morte)
    // -------------------------------------------------
    public array $causasSelecionadas = [];
    public array $listaCausas = [];

    // -------------------------------------------------
    // Filtros de busca (tudo com prefixo search*)
    // -------------------------------------------------
    public string $searchNome = '';                 // nome_falecido
    public ?string $searchMae = null;
    public ?string $searchPai = null;
    public ?string $searchQuadra = null;
    public ?string $searchFila = null;
    public ?string $searchCova = null;

    public ?string $searchFalecimentoDe = null;
    public ?string $searchFalecimentoAte = null;
    public ?string $searchSepultamentoDe = null;
    public ?string $searchSepultamentoAte = null;

    public ?string $searchStatus = null;       // 'ativo', 'inativo' ou null
    public int $perPage = 10;

    // Filtros adicionais (checkboxes)
    public bool $filtroIndigente = false;
    public bool $filtroNatimorto = false;
    public bool $filtroTranslado = false;
    public bool $filtroMembro = false;

    // -------------------------------------------------
    // Regras de validação (CRUD)
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
    // Hooks reativos (resetar paginação ao filtrar)
    // -------------------------------------------------
    public function updated($name, $value)
    {
        // Qualquer campo que começar com 'search*' ou perPage deve resetar a paginação
        if (str_starts_with($name, 'search') || $name === 'perPage') {
            $this->resetPage();
        }
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

    public function resetForm(string $context = 'crud'): void
    {
        $crudFields = [
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
        ];

        $searchFields = [
            'searchNome',
            'searchMae',
            'searchPai',
            'searchQuadra',
            'searchFila',
            'searchCova',
            'searchFalecimentoDe',
            'searchFalecimentoAte',
            'searchSepultamentoDe',
            'searchSepultamentoAte',
            'searchStatus',
            'filtroIndigente',
            'filtroNatimorto',
            'filtroTranslado',
            'filtroMembro',
        ];

        $fieldsToReset = match ($context) {
            'crud' => $crudFields,
            'search' => $searchFields,
            'all' => array_merge($crudFields, $searchFields),
            default => $crudFields,
        };

        $this->reset($fieldsToReset);

        if (in_array($context, ['crud', 'all'])) {
            $this->ativo = true;
            $this->indigente = $this->natimorto = $this->translado = $this->membro = false;
            $this->causasSelecionadas = [];
        }

        if (in_array($context, ['search', 'all'])) {
            $this->resetPage();
        }
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
            ->when($this->searchNome, fn ($q) => $q->where('nome_falecido', 'like', "%{$this->searchNome}%"))
            ->when($this->searchMae, fn ($q) => $q->where('mae', 'like', "%{$this->searchMae}%"))
            ->when($this->searchPai, fn ($q) => $q->where('pai', 'like', "%{$this->searchPai}%"))
            ->when($this->searchQuadra, fn ($q) => $q->where('quadra', 'like', "%{$this->searchQuadra}%"))
            ->when($this->searchFila, fn ($q) => $q->where('fila', 'like', "%{$this->searchFila}%"))
            ->when($this->searchCova, fn ($q) => $q->where('cova', 'like', "%{$this->searchCova}%"))
            ->when($this->searchFalecimentoDe, fn ($q) => $q->whereDate('data_falecimento', '>=', $this->searchFalecimentoDe))
            ->when($this->searchFalecimentoAte, fn ($q) => $q->whereDate('data_falecimento', '<=', $this->searchFalecimentoAte))
            ->when($this->searchSepultamentoDe, fn ($q) => $q->whereDate('data_sepultamento', '>=', $this->searchSepultamentoDe))
            ->when($this->searchSepultamentoAte, fn ($q) => $q->whereDate('data_sepultamento', '<=', $this->searchSepultamentoAte))
            ->when($this->searchStatus === 'ativo', fn ($q) => $q->where('ativo', true))
            ->when($this->searchStatus === 'inativo', fn ($q) => $q->where('ativo', false))
            ->when(
                $this->filtroIndigente || $this->filtroNatimorto || $this->filtroTranslado || $this->filtroMembro,
                function ($consulta) {
                    $consulta->where(function ($subconsulta) {
                        if ($this->filtroIndigente) {
                            $subconsulta->orWhere('indigente', true);
                        }
                        if ($this->filtroNatimorto) {
                            $subconsulta->orWhere('natimorto', true);
                        }
                        if ($this->filtroTranslado) {
                            $subconsulta->orWhere('translado', true);
                        }
                        if ($this->filtroMembro) {
                            $subconsulta->orWhere('membro', true);
                        }
                    });
                }
            )

            ->orderByDesc('data_sepultamento')
            ->paginate($this->perPage);

        return view('livewire.empresa.sepultamento.index', compact('sepultamentos'));
    }
}
