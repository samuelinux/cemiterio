<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public int $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage($value)
    {
        $this->perPage = (int) $value; // cast explícito
        $this->resetPage();            // evita cair em página inválida
    }

    public function confirmDelete(int $userId)
    {
        $user = User::findOrFail($userId);

        // Dispara SweetAlert de confirmação via Alpine
        $this->dispatch('swal-confirm-delete',
            id: $user->id,
            title: 'Excluir usuário?',
            text: "Confirma excluir {$user->name}?"
        );
    }

    public function delete(int $userId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->delete();

            $this->dispatch('toast', type: 'success', title: 'Usuário excluído!');
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
        $users = User::query()
            ->when($this->search, function ($q) {
                $q->where(function ($w) {
                    $w->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('livewire.admin.user-index', compact('users'));
    }
}
