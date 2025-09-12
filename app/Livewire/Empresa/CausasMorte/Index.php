<?php

namespace App\Livewire\Empresa\CausasMorte;

use App\Models\AuditLog;
use App\Models\CausaMorte;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // -------------------------------------------------
    // Filtro & paginação
    // -------------------------------------------------
    #[Url(as: 'q')]
    public string $search = '';

    public int $perPage = 10;

    // -------------------------------------------------
    // Flags de UI (modais)
    // -------------------------------------------------
    public bool $showCreateModal = false;
    public bool $showEditModal   = false;

    // -------------------------------------------------
    // Formulário (reutilizado para criar/editar)
    // -------------------------------------------------
    public ?int $causaId = null;
    public string $descricao = '';
    public ?string $codigo_cid10 = null;
    public bool $ativo = true;

    // -------------------------------------------------
    // Controle de permissão de listagem (UX)
    // -------------------------------------------------
    public bool $canList = true; // bloqueia a UI se usuário não puder "consultar"

    // -------------------------------------------------
    // Validações
    // -------------------------------------------------
    protected function rules(): array
    {
        return [
            'descricao'     => 'required|string|max:255',
            'codigo_cid10'  => 'nullable|string|max:20',
            'ativo'         => 'boolean',
        ];
    }

    protected array $messages = [
        'descricao.required' => 'A descrição é obrigatória.',
        'descricao.max'      => 'A descrição não pode exceder 255 caracteres.',
        'codigo_cid10.max'   => 'O código CID-10 não pode exceder 20 caracteres.',
    ];

    // -------------------------------------------------
    // Lifecycle / Hooks
    // -------------------------------------------------
    public function mount(): void
    {
        // Permissão para LISTAR
        if (!$this->ensurePermission('consultar', toastOnDeny: true)) {
            $this->canList = false;
        }
    }

    public function updatingSearch(): void
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
        if (!$this->ensurePermission('cadastrar', toastOnDeny: true)) {
            return;
        }

        $this->resetValidation();
        $this->resetForm();
        $this->logAcao('ui.open_create_modal');
        $this->showCreateModal = true;
    }

    public function store(): void
    {
        if (!$this->ensurePermission('cadastrar', toastOnDeny: true)) {
            return;
        }

        try {
            $this->validate();

            $c = CausaMorte::create([
                'descricao'     => $this->descricao,
                'codigo_cid10'  => $this->codigo_cid10,
                'ativo'         => $this->ativo,
            ]);

            // Trait Auditavel fará o log 'create'
            $this->logAcao('create.success', $c->id);

            $this->dispatch('toast', type: 'success', title: 'Causa criada com sucesso!');
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

    public function edit(int $id): void
    {
        if (!$this->ensurePermission('editar', toastOnDeny: true)) {
            return;
        }

        $this->resetValidation();

        $c = CausaMorte::findOrFail($id);

        $this->causaId       = $c->id;
        $this->descricao     = (string) $c->descricao;
        $this->codigo_cid10  = (string) $c->codigo_cid10;
        $this->ativo         = (bool) $c->ativo;

        $this->logAcao('ui.open_edit_modal', $id);
        $this->showEditModal = true;
    }

    public function update(): void
    {
        if (!$this->ensurePermission('editar', toastOnDeny: true)) {
            return;
        }

        if (!$this->causaId) {
            $this->dispatch('swal', type: 'error', title: 'Registro inválido.');
            return;
        }

        try {
            $this->validate();

            CausaMorte::findOrFail($this->causaId)->update([
                'descricao'     => $this->descricao,
                'codigo_cid10'  => $this->codigo_cid10,
                'ativo'         => $this->ativo,
            ]);

            // Trait Auditavel fará o log 'update'
            $this->logAcao('update.success', $this->causaId);

            $this->dispatch('toast', type: 'success', title: 'Causa atualizada!');
            $this->closeModals();
            $this->resetForm();
        } catch (ValidationException $e) {
            $this->logAcao('update.validation_failed', $this->causaId, null, ['errors' => $e->validator->errors()->toArray()]);
            $lista = collect($e->validator->errors()->all())->map(fn ($m) => "<li>{$m}</li>")->implode('');
            $this->dispatch('swal', type: 'error', title: 'Erros de validação', html: "<ul class='list-disc pl-5 text-left'>{$lista}</ul>");
            throw $e;
        } catch (\Throwable $e) {
            $this->logAcao('update.error', $this->causaId, null, ['message' => $e->getMessage()]);
            report($e);
            $this->dispatch('swal', type: 'error', title: 'Falha ao salvar', text: app()->isLocal() ? $e->getMessage() : 'Erro inesperado.');
        }
    }

    public function confirmDelete(int $id): void
    {
        if (!$this->ensurePermission('excluir', toastOnDeny: true)) {
            return;
        }

        $c = CausaMorte::findOrFail($id);

        $this->logAcao('delete.confirm', $c->id);
        $this->dispatch('swal-confirm-delete',
            id: $c->id,
            title: 'Excluir causa?',
            text: "Confirma excluir a causa: {$c->descricao}?"
        );
    }

    public function delete(int $id): void
    {
        if (!$this->ensurePermission('excluir', toastOnDeny: true)) {
            return;
        }

        try {
            CausaMorte::findOrFail($id)->delete();

            // Trait Auditavel fará o log 'delete'
            $this->logAcao('delete.success', $id);

            $this->dispatch('toast', type: 'success', title: 'Causa excluída!');
            $this->resetPage();
        } catch (\Throwable $e) {
            $this->logAcao('delete.error', $id, null, ['message' => $e->getMessage()]);
            report($e);
            $this->dispatch('swal', type: 'error', title: 'Falha ao excluir', text: app()->isLocal() ? $e->getMessage() : 'Erro inesperado.');
        }
    }

    // -------------------------------------------------
    // Utilitários
    // -------------------------------------------------
    public function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal   = false;
    }

    private function resetForm(): void
    {
        $this->reset(['causaId', 'descricao', 'codigo_cid10', 'ativo']);
        $this->ativo = true;
    }

    private function ensurePermission(string $acao, bool $toastOnDeny = false): bool
    {
        $u = Auth::user();

        // Admin tem acesso total
        if ($u && method_exists($u, 'isAdmin') && $u->isAdmin()) {
            return true;
        }

        // Usuário precisa estar autenticado e ter permissão na tabela 'causas_morte'
        $p = $u?->permissoes()->where('tabela', 'causas_morte')->first();

        $tem = match ($acao) {
            'consultar' => (bool) ($p->consultar ?? false),
            'cadastrar' => (bool) ($p->cadastrar ?? false),
            'editar'    => (bool) ($p->editar ?? false),
            'excluir'   => (bool) ($p->excluir ?? false),
            default     => false,
        };

        if (! $tem) {
            $this->logAcao('permission.denied', null, null, ['acao' => $acao]);
            if ($toastOnDeny) {
                $this->dispatch('toast', type: 'error', title: 'Sem permissão para esta ação.');
            }
        }

        return $tem;
    }

    private function logAcao(
        string $acao,
        ?int $registroId = null,
        ?array $antes = null,
        ?array $depois = null,
        string $tabela = 'causas_morte'
    ): void {
        try {
            AuditLog::create([
                'user_id'       => Auth::id(),
                'empresa_id'    => Auth::user()->empresa_id ?? null,
                'tabela'        => $tabela,
                'registro_id'   => $registroId,
                'acao'          => $acao,
                'valores_antes' => $antes,
                'valores_depois'=> $depois,
                'ip'            => request()->ip(),
                'user_agent'    => request()->userAgent(),
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
        // Se não pode listar, não consulta o DB (UX + perf)
        if (! $this->canList) {
            // Pode retornar uma coleção vazia para a view
            $causas = collect();
            return view('livewire.empresa.causas-morte-index', compact('causas'));
        }

        $causas = CausaMorte::query()
            ->when($this->search, fn ($q) =>
                $q->where('descricao', 'like', "%{$this->search}%")
                  ->orWhere('codigo_cid10', 'like', "%{$this->search}%")
            )
            ->orderBy('descricao')
            ->paginate($this->perPage);

        return view('livewire.empresa.causas-morte-index', compact('causas'));
    }
}
