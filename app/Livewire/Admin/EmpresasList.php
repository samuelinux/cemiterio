<?php

namespace App\Livewire\Admin;

use App\Models\Empresa;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EmpresasList extends Component
{
    use WithPagination, LivewireAlert;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'nome';
    public $sortDirection = 'asc';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function toggleStatus($empresaId)
    {
        $empresa = Empresa::findOrFail($empresaId);
        $empresa->update(['ativo' => !$empresa->ativo]);
        
        $status = $empresa->ativo ? 'ativada' : 'desativada';
        $this->alert('success', "Empresa {$status} com sucesso!");
    }

    public function delete($empresaId)
    {
        $empresa = Empresa::findOrFail($empresaId);
        $empresa->delete();
        
        $this->alert('success', 'Empresa excluída com sucesso!');
    }

    public function render()
    {
        $empresas = Empresa::query()
            ->when($this->search, function ($query) {
                $query->where('nome', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('cnpj', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.empresas-list', compact('empresas'));
    }
}
