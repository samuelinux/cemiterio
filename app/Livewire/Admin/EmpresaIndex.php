<?php

namespace App\Livewire\Admin;

use App\Models\Empresa;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class EmpresaIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public int $perPage = 10;

    public ?string $status = null; // 'ativa' | 'inativa' | null

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
        $this->perPage = (int) $value; // cast explícito
        $this->resetPage();            // evita cair em página inválida
    }

    public function confirmDelete(int $empresaId)
    {
        $empresa = Empresa::findOrFail($empresaId);

        $this->dispatch('swal-confirm-delete',
            id: $empresa->id,
            title: 'Excluir empresa?',
            text: "Confirma excluir {$empresa->nome}?"
        );
    }

    public function delete(int $empresaId)
    {
        try {
            $empresa = Empresa::findOrFail($empresaId);
            $empresa->delete();

            $this->dispatch('toast', type: 'success', title: 'Empresa excluída!');
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
        $empresas = Empresa::query()
            ->when($this->search, function ($q) {
                $q->where(function ($w) {
                    $w->where('nome', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('cidade', 'like', "%{$this->search}%")
                      ->orWhere('estado', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status === 'ativa', fn ($q) => $q->where('ativo', true))
            ->when($this->status === 'inativa', fn ($q) => $q->where('ativo', false))
            ->orderBy('nome')
            ->paginate($this->perPage);

        return view('livewire.admin.empresa-index', compact('empresas'));
    }
}
