<?php

namespace App\Livewire\Empresa\Sepultamentos\Traits;

use Livewire\Attributes\Url;

trait WithSepultamentoFilters
{
    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public ?string $status = null; // 'ativo' | 'inativo' | null

    public int $perPage = 10;

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
}
