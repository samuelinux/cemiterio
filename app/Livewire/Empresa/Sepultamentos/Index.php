<?php

namespace App\Livewire\Empresa\Sepultamentos;

use App\Models\Sepultamento;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public int $perPage = 10;

    public ?string $status = null; // 'ativo' | 'inativo' | null

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatedPerPage($value)
    {
        $this->perPage = (int) $value;
        $this->resetPage();
    }

    public function confirmDelete(int $sepultamentoId): void
    {
        $s = Sepultamento::findOrFail($sepultamentoId);

        $this->dispatch('swal-confirm-delete',
            id: $s->id,
            title: 'Excluir sepultamento?',
            text: "Confirma excluir o sepultamento de {$s->nome_falecido}?"
        );
    }

    public function delete(int $sepultamentoId): void
    {
        try {
            $user = Auth::user();

            if (!$user->hasPermissao('sepultamentos', 'excluir')) {
                $this->dispatch('toast', type: 'error', title: 'Você não tem permissão para excluir sepultamentos.');

                return;
            }

            $empresaId = Auth::user()->empresa_id;

            $sepultamento = Sepultamento::where('empresa_id', $empresaId)
                ->findOrFail($sepultamentoId);

            $sepultamento->delete(); // soft delete

            $this->dispatch('toast', type: 'success', title: 'Sepultamento excluído!');
            $this->resetPage();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('swal',
                type: 'error',
                title: 'Falha ao excluir',
                text: app()->isLocal() ? $e->getMessage() : 'Erro inesperado.'
            );
        }
    }

    public function render()
    {
        $user = Auth::user();

        if (!$user->hasPermissao('sepultamentos', 'consultar')) {
            $sepultamentos = new LengthAwarePaginator([], 0, $this->perPage);

            // dispara toast depois que a view já está pronta
            $this->js("
            const T = Swal.mixin({toast:true, position:'top-end', showConfirmButton:false, timer:4000, timerProgressBar:true});
            T.fire({ icon: 'error', title: 'Você não tem permissão para listar sepultamentos.' })
        ");

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
