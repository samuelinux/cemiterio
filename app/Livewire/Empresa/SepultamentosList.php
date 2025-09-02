<?php

namespace App\Livewire\Empresa;

use App\Models\Sepultamento;
use App\Traits\HasPermissions;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SepultamentosList extends Component
{
    use WithPagination;
    use HasPermissions;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'data_sepultamento';
    public $sortDirection = 'desc';
    public $filterDataInicio = '';
    public $filterDataFim = '';
    public $filterTipo = '';

    protected $queryString = ['search'];

    public function mount()
    {
        $this->checkPermission('sepultamentos', 'consultar');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterDataInicio()
    {
        $this->resetPage();
    }

    public function updatingFilterDataFim()
    {
        $this->resetPage();
    }

    public function updatingFilterTipo()
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

    public function clearFilters()
    {
        $this->search = '';
        $this->filterDataInicio = '';
        $this->filterDataFim = '';
        $this->filterTipo = '';
        $this->resetPage();
    }

    public function delete($sepultamentoId)
    {
        $this->checkPermission('sepultamentos', 'excluir');

        $sepultamento = Sepultamento::where('empresa_id', Auth::user()->empresa_id)
            ->findOrFail($sepultamentoId);
        $sepultamento->delete();

        $this->alert('success', 'Sepultamento excluído com sucesso!');
    }

    public function render()
    {
        $sepultamentos = Sepultamento::query()
            ->where('empresa_id', Auth::user()->empresa_id)
            ->with(['user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nome_falecido', 'like', '%'.$this->search.'%')
                      ->orWhere('cpf_falecido', 'like', '%'.$this->search.'%')
                      ->orWhere('nome_responsavel', 'like', '%'.$this->search.'%')
                      ->orWhere('local_sepultamento', 'like', '%'.$this->search.'%')
                      ->orWhere('numero_sepultura', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterDataInicio, function ($query) {
                $query->whereDate('data_sepultamento', '>=', $this->filterDataInicio);
            })
            ->when($this->filterDataFim, function ($query) {
                $query->whereDate('data_sepultamento', '<=', $this->filterDataFim);
            })
            ->when($this->filterTipo, function ($query) {
                $query->where('tipo_sepultamento', $this->filterTipo);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.empresa.sepultamentos-list', compact('sepultamentos'));
    }
}
