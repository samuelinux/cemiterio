<?php

namespace App\Livewire\Empresa\Sepultamentos;

use App\Models\AuditLog;
use App\Models\CausaMorte;
use App\Models\Sepultamento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    // -------------------------------------------------
    // Filtros & paginação
    // -------------------------------------------------
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public ?string $status = null; // 'ativo' | 'inativo' | null

    public int $perPage = 10;

    // -------------------------------------------------
    // Flags de UI (modais)
    // -------------------------------------------------
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showViewModal = false;

    // -------------------------------------------------
    // Controle de permissão (exibir botões/UX)
    // -------------------------------------------------
    public bool $canList = true;
    public bool $canCreate = false;
    public bool $canEdit = false;
    public bool $canDelete = false;

    // -------------------------------------------------
    // Formulário (reutilizado para criar/editar/ver)
    // -------------------------------------------------
    public ?int $sepultamentoId = null;

    public string $nome_falecido = '';
    public ?string $mae = null;
    public ?string $pai = null;

    public bool $indigente = false;
    public bool $natimorto = false;
    public bool $translado = false;
    public bool $membro = false;

    public ?string $data_falecimento = null;   // Y-m-d
    public ?string $data_sepultamento = null;  // Y-m-d

    public ?string $quadra = null;
    public ?string $fila = null;
    public ?string $cova = null;

    public ?string $certidao_obito_path = null;
    public ?TemporaryUploadedFile $certidao_obito = null;
    public ?string $observacoes = null;

    public bool $ativo = true;

    // Causas (pivot)
    public array $causasSelecionadas = [];
    public array $listaCausas = [];

    // -------------------------------------------------
    // Validação
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
    // Lifecycle / Hooks
    // -------------------------------------------------
    public function mount(): void
    {
        $u = Auth::user();

        $this->canList = $u->hasPermissao('sepultamentos', 'consultar');
        $this->canCreate = $u->hasPermissao('sepultamentos', 'cadastrar');
        $this->canEdit = $u->hasPermissao('sepultamentos', 'editar');
        $this->canDelete = $u->hasPermissao('sepultamentos', 'excluir');

        if (!$this->canList) {
            // evita consulta ao DB e links()
            $this->dispatch('toast', type: 'error', title: 'Você não tem permissão para listar sepultamentos.');
        }

        $this->carregarListaCausas();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $this->perPage = (int) $value;
        $this->resetPage();
    }

    // -------------------------------------------------
    // Ações de UI / CRUD
    // -------------------------------------------------
    public function create(): void
    {
        if (!$this->canCreate) {
            $this->logAcao('permission.denied', null, null, ['acao' => 'cadastrar']);
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para cadastrar sepultamentos.');

            return;
        }

        $this->resetValidation();
        $this->resetForm();
        $this->logAcao('ui.open_create_modal');
        $this->showCreateModal = true;
    }

    public function store(): void
    {
        if (!$this->canCreate) {
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para cadastrar.');

            return;
        }

        try {
            $this->validate();

            $empresaId = Auth::user()->empresa_id;
            $userId = Auth::id();

            DB::transaction(function () use ($empresaId, $userId) {
                $data = [
                    'empresa_id' => $empresaId,
                    'user_id' => $userId,
                    'nome_falecido' => $this->nome_falecido,
                    'mae' => $this->mae,
                    'pai' => $this->pai,
                    'indigente' => $this->indigente,
                    'natimorto' => $this->natimorto,
                    'translado' => $this->translado,
                    'membro' => $this->membro,
                    'data_falecimento' => $this->data_falecimento,
                    'data_sepultamento' => $this->data_sepultamento,
                    'quadra' => $this->quadra,
                    'fila' => $this->fila,
                    'cova' => $this->cova,
                    'observacoes' => $this->observacoes,
                    'ativo' => $this->ativo,
                ];

                if ($this->certidao_obito) {
                    $data['certidao_obito_path'] = $this->certidao_obito->store('certidoes', 'public');
                }

                $s = Sepultamento::create($data);

                // Pivot causas
                $s->causas()->sync($this->causasSelecionadas ?? []);

                $this->logAcao('create.success', $s->id);
            });

            $this->dispatch('toast', type: 'success', title: 'Sepultamento cadastrado!');
            $this->closeModals();
            $this->resetPage();
            $this->resetForm();
        } catch (ValidationException $e) {
            $this->logAcao('create.validation_failed', null, null, ['errors' => $e->validator->errors()->toArray()]);
            $lista = collect($e->validator->errors()->all())->map(fn ($m) => "<li>{$m}</li>")->implode('');
            $this->dispatch('swal', type: 'error', title: 'Erros de validação', html: "<ul class='list-disc pl-5 text-left'>{$lista}</ul>");
            throw $e;
        } catch (\Throwable $e) {
            $this->logAcao('create.error', null, null, ['message' => $e->getMessage()]);
            report($e);
            $this->dispatch('swal', type: 'error', title: 'Falha ao salvar', text: app()->isLocal() ? $e->getMessage() : 'Erro inesperado.');
        }
    }

    public function show(int $id): void
    {
        if (!$this->canList) {
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para consultar.');

            return;
        }

        $empresaId = Auth::user()->empresa_id;

        $s = Sepultamento::with('causas')
            ->where('empresa_id', $empresaId)
            ->findOrFail($id);

        $this->preencherFormulario($s);
        $this->logAcao('ui.open_view_modal', $id);
        $this->showViewModal = true;
    }

    public function edit(int $id): void
    {
        if (!$this->canEdit) {
            $this->logAcao('permission.denied', $id, null, ['acao' => 'editar']);
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para editar.');

            return;
        }

        $this->resetValidation();

        $empresaId = Auth::user()->empresa_id;

        $s = Sepultamento::with('causas')
            ->where('empresa_id', $empresaId)
            ->findOrFail($id);

        $this->preencherFormulario($s);

        $this->logAcao('ui.open_edit_modal', $id);
        $this->showEditModal = true;
    }

    public function update(): void
    {
        if (!$this->canEdit) {
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para editar.');

            return;
        }

        if (!$this->sepultamentoId) {
            $this->dispatch('swal', type: 'error', title: 'Registro inválido.');

            return;
        }

        try {
            $this->validate();

            $empresaId = Auth::user()->empresa_id;

            DB::transaction(function () use ($empresaId) {
                $s = Sepultamento::where('empresa_id', $empresaId)
                    ->findOrFail($this->sepultamentoId);

                $antes = $s->getOriginal();

                $data = [
                    'nome_falecido' => $this->nome_falecido,
                    'mae' => $this->mae,
                    'pai' => $this->pai,
                    'indigente' => $this->indigente,
                    'natimorto' => $this->natimorto,
                    'translado' => $this->translado,
                    'membro' => $this->membro,
                    'data_falecimento' => $this->data_falecimento,
                    'data_sepultamento' => $this->data_sepultamento,
                    'quadra' => $this->quadra,
                    'fila' => $this->fila,
                    'cova' => $this->cova,
                    'observacoes' => $this->observacoes,
                    'ativo' => $this->ativo,
                ];

                if ($this->certidao_obito) {
                    if ($s->certidao_obito_path) {
                        Storage::disk('public')->delete($s->certidao_obito_path);
                    }
                    $data['certidao_obito_path'] = $this->certidao_obito->store('certidoes', 'public');
                }

                $s->update($data);

                // Pivot causas
                $s->causas()->sync($this->causasSelecionadas ?? []);

                $this->logAcao('update.success', $s->id, $antes, $s->getChanges());
            });

            $this->dispatch('toast', type: 'success', title: 'Sepultamento atualizado!');
            $this->closeModals();
            $this->resetForm();
        } catch (ValidationException $e) {
            $this->logAcao('update.validation_failed', $this->sepultamentoId, null, ['errors' => $e->validator->errors()->toArray()]);
            $lista = collect($e->validator->errors()->all())->map(fn ($m) => "<li>{$m}</li>")->implode('');
            $this->dispatch('swal', type: 'error', title: 'Erros de validação', html: "<ul class='list-disc pl-5 text-left'>{$lista}</ul>");
            throw $e;
        } catch (\Throwable $e) {
            $this->logAcao('update.error', $this->sepultamentoId, null, ['message' => $e->getMessage()]);
            report($e);
            $this->dispatch('swal', type: 'error', title: 'Falha ao salvar', text: app()->isLocal() ? $e->getMessage() : 'Erro inesperado.');
        }
    }

    public function confirmDelete(int $sepultamentoId): void
    {
        if (!$this->canDelete) {
            $this->logAcao('permission.denied', $sepultamentoId, null, ['acao' => 'excluir']);
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para excluir.');

            return;
        }

        $empresaId = Auth::user()->empresa_id;
        $s = Sepultamento::where('empresa_id', $empresaId)->findOrFail($sepultamentoId);

        $this->logAcao('delete.confirm', $s->id);

        $this->dispatch('swal-confirm-delete',
            id: $s->id,
            title: 'Excluir sepultamento?',
            text: "Confirma excluir o sepultamento de {$s->nome_falecido}?"
        );
    }

    public function delete(int $sepultamentoId): void
    {
        try {
            if (!$this->canDelete) {
                $this->dispatch('toast', type: 'error', title: 'Você não tem permissão para excluir sepultamentos.');

                return;
            }

            $empresaId = Auth::user()->empresa_id;

            $sepultamento = Sepultamento::where('empresa_id', $empresaId)
                ->findOrFail($sepultamentoId);

            if ($sepultamento->certidao_obito_path) {
                Storage::disk('public')->delete($sepultamento->certidao_obito_path);
            }

            $sepultamento->delete(); // soft delete

            $this->logAcao('delete.success', $sepultamentoId);

            $this->dispatch('toast', type: 'success', title: 'Sepultamento excluído!');
            $this->resetPage();
        } catch (\Throwable $e) {
            $this->logAcao('delete.error', $sepultamentoId, null, ['message' => $e->getMessage()]);
            report($e);
            $this->dispatch('swal',
                type: 'error',
                title: 'Falha ao excluir',
                text: app()->isLocal() ? $e->getMessage() : 'Erro inesperado.'
            );
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

    private function logAcao(
        string $acao,
        ?int $registroId = null,
        ?array $antes = null,
        ?array $depois = null,
        string $tabela = 'sepultamentos'
    ): void {
        try {
            AuditLog::create([
                'user_id' => Auth::id(),
                'empresa_id' => Auth::user()->empresa_id ?? null,
                'tabela' => $tabela,
                'registro_id' => $registroId,
                'acao' => $acao,
                'valores_antes' => $antes,
                'valores_depois' => $depois,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            report($e); // não quebra a UX se auditoria falhar
        }
    }

    // -------------------------------------------------
    // Render
    // -------------------------------------------------
    public function render()
    {
        if (!$this->canList) {
            // paginator vazio (0 resultados) – evita erro no links()
            $sepultamentos = Sepultamento::query()
                ->whereRaw('1=0')
                ->paginate($this->perPage);

            return view('livewire.empresa.sepultamentos-index', compact('sepultamentos'));
        }

        $empresaId = Auth::user()->empresa_id;

        $sepultamentos = Sepultamento::query()
            ->where('empresa_id', $empresaId)
            ->when($this->search, function ($q) {
                $q->where(function ($w) {
                    $w->where('nome_falecido', 'like', "%{$this->search}%")
                      ->orWhere('quadra', 'like', "%{$this->search}%")
                      ->orWhere('fila', 'like', "%{$this->search}%")
                      ->orWhere('cova', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status === 'ativo', fn ($q) => $q->where('ativo', true))
            ->when($this->status === 'inativo', fn ($q) => $q->where('ativo', false))
            ->orderByDesc('data_sepultamento')
            ->paginate($this->perPage);

        return view('livewire.empresa.sepultamentos-index', compact('sepultamentos'));
    }
}
