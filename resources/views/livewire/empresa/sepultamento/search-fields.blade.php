{{-- resources/views/livewire/empresa/sepultamentos/search-fields.blade.php --}}
<div x-data="{ showFilters: false }" class="space-y-3">
    {{-- Linha principal: busca + botão filtros --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <input type="text"
               placeholder="Buscar por nome do falecido..."
               wire:model.live.debounce.300ms="search"
               class="w-full sm:w-80 rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 px-3 py-2 text-sm">

        <div class="flex gap-2">
            <button type="button"
                    @click="showFilters = !showFilters"
                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 019 17V13.414L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filtros
            </button>

            {{-- Select por página --}}
            <select wire:model.live="perPage"
                    class="rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
                <option value="10">10/página</option>
                <option value="25">25/página</option>
                <option value="50">50/página</option>
            </select>
        </div>
    </div>

    {{-- Filtros avançados --}}
    <div x-show="showFilters" x-collapse x-cloak
         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 bg-gray-50 border rounded-md p-4">

        {{-- Mãe --}}
        <div>
            <label class="block text-xs font-medium text-gray-600">Mãe</label>
            <input type="text" wire:model.live="filter_mae"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>

        {{-- Pai --}}
        <div>
            <label class="block text-xs font-medium text-gray-600">Pai</label>
            <input type="text" wire:model.live="filter_pai"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>

        {{-- Data de falecimento --}}
        <div>
            <label class="block text-xs font-medium text-gray-600">Falecimento (de)</label>
            <input type="date" wire:model.live="filter_data_falecimento_de"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Falecimento (até)</label>
            <input type="date" wire:model.live="filter_data_falecimento_ate"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>

        {{-- Data de sepultamento --}}
        <div>
            <label class="block text-xs font-medium text-gray-600">Sepultamento (de)</label>
            <input type="date" wire:model.live="filter_data_sepultamento_de"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Sepultamento (até)</label>
            <input type="date" wire:model.live="filter_data_sepultamento_ate"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>

        {{-- Localização --}}
        <div>
            <label class="block text-xs font-medium text-gray-600">Quadra</label>
            <input type="text" wire:model.live="filter_quadra"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Fila</label>
            <input type="text" wire:model.live="filter_fila"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Cova</label>
            <input type="text" wire:model.live="filter_cova"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-xs font-medium text-gray-600">Status</label>
            <select wire:model.live="status"
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 text-sm">
                <option value="">Todos</option>
                <option value="ativo">Ativos</option>
                <option value="inativo">Inativos</option>
            </select>
        </div>
    </div>
</div>
