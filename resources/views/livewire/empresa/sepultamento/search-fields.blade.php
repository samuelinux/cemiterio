<div x-data="{ showFilters: false }" class="space-y-3">
    {{-- Linha principal: busca + botões filtros, limpar e exportar --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-2">
        <input type="text" placeholder="Buscar por nome do falecido..." wire:model.live.debounce.500ms="searchNome"
            class="w-full sm:w-80 rounded-md border-gray-300 shadow-sm 
                      focus:border-gray-500 focus:ring-gray-500 px-3 py-2 text-sm">

        <div class="flex gap-2">
            <button type="button" @click="showFilters = !showFilters"
                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md 
                           hover:bg-gray-200 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 13.414V19a1 1 0 01-1.447.894l-4-2A1 1 0 019 17V13.414L3.293 6.707A1 1 0 013 6V4z" />
                </svg>
                Filtros
            </button>

            <button wire:click="resetForm('search')"
                class="px-3 py-2 bg-gray-500 text-white rounded-md 
                           hover:bg-gray-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Limpar
            </button>

            <button wire:click="exportToPdf"
                class="px-3 py-2 bg-blue-600 text-white rounded-md 
                           hover:bg-blue-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar PDF
            </button>

            {{-- Select por página --}}
            <select wire:model.live.debounce.500ms="perPage"
                class="rounded-md border-gray-300 shadow-sm 
                           focus:border-gray-500 focus:ring-gray-500 text-sm">
                <option value="10">10/página</option>
                <option value="25">25/página</option>
                <option value="50">50/página</option>
            </select>
        </div>
    </div>

    {{-- Filtros avançados --}}
<div x-show="showFilters" x-collapse x-cloak
    class="space-y-4 bg-gray-300 border rounded-md p-4">

    {{-- Grupo: Filiação --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 border-b pb-3">
        <div>
            <label class="block text-xs font-medium text-gray-600">Mãe</label>
            <input type="text" wire:model.live.debounce.500ms="searchMae"
                class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                       focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Pai</label>
            <input type="text" wire:model.live.debounce.500ms="searchPai"
                class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                       focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
    </div>

    {{-- Grupo: Data do Sepultamento (Ano/Mês/Dia) --}} <!--
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 border-b pb-3">

        {{-- Dia --}}
        <div>
            <label class="block text-xs font-medium text-gray-600">Dia do Sepultamento</label>
            <input type="number" wire:model.live.debounce.500ms="searchDia"
                min="1" max="31"
                class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                       focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>

        {{-- Mês --}}
        <div>
            <label class="block text-xs font-medium text-gray-600">Mês do Sepultamento</label>
            <input type="number" wire:model.live.debounce.500ms="searchMes"
                min="1" max="12"
                class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                       focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
        {{-- Ano --}}
        <div>
            <label class="block text-xs font-medium text-gray-600">Ano do Sepultamento</label>
            <input type="number" wire:model.live.debounce.500ms="searchAno"
                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').slice(0,4)"
                class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                       focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
        
        
    </div> -->

    {{-- Grupo: Datas (falecimento / sepultamento de-ate) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 border-b pb-3">
    <div>
        <label class="block text-xs font-medium text-gray-600">Falecimento (de)</label>
        <input type="text" wire:model.live.debounce.500ms="searchFalecimentoDe"
            placeholder="dd/mm/aaaa"
            x-data
            x-mask="99/99/9999"
            class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                   focus:border-gray-500 focus:ring-gray-500 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600">Falecimento (até)</label>
        <input type="text" wire:model.live.debounce.500ms="searchFalecimentoAte"
            placeholder="dd/mm/aaaa"
            x-data
            x-mask="99/99/9999"
            class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                   focus:border-gray-500 focus:ring-gray-500 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600">Sepultamento (de)</label>
        <input type="text" wire:model.live.debounce.500ms="searchSepultamentoDe"
            placeholder="dd/mm/aaaa"
            x-data
            x-mask="99/99/9999"
            class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                   focus:border-gray-500 focus:ring-gray-500 text-sm">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600">Sepultamento (até)</label>
        <input type="text" wire:model.live.debounce.500ms="searchSepultamentoAte"
            placeholder="dd/mm/aaaa"
            x-data
            x-mask="99/99/9999"
            class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                   focus:border-gray-500 focus:ring-gray-500 text-sm">
    </div>
</div>

    {{-- Grupo: Localização --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 border-b pb-3">
        <div>
            <label class="block text-xs font-medium text-gray-600">Quadra</label>
            <input type="text" wire:model.live.debounce.500ms="searchQuadra"
                class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                       focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Fila</label>
            <input type="text" wire:model.live.debounce.500ms="searchFila"
                class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                       focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600">Cova</label>
            <input type="text" wire:model.live.debounce.500ms="searchCova"
                class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                       focus:border-gray-500 focus:ring-gray-500 text-sm">
        </div>
    </div>

    {{-- Grupo: Status --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-600">Status</label>
            <select wire:model.live.debounce.500ms="searchStatus"
                class="mt-1 w-full rounded-md border-gray-300 shadow-sm
                       focus:border-gray-500 focus:ring-gray-500 text-sm">
                <option value="">Todos</option>
                <option value="ativo">Ativos</option>
                <option value="inativo">Inativos</option>
            </select>
        </div>
    </div>

    {{-- Grupo: Classificação --}}
    {{-- Classificação do sepultamento --}}
<div class="sm:col-span-2">
    <label class="block text-xs font-medium text-gray-600">Classificação do sepultamento</label>
    <div class="mt-2 flex flex-wrap gap-4">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" wire:model.live="filtroIndigente"
                   class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
            <span class="text-sm text-gray-800">Indigente</span>
        </label>

        <label class="inline-flex items-center gap-2">
            <input type="checkbox" wire:model.live="filtroNatimorto"
                   class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
            <span class="text-sm text-gray-800">Natimorto</span>
        </label>

        <label class="inline-flex items-center gap-2">
            <input type="checkbox" wire:model.live="filtroTranslado"
                   class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
            <span class="text-sm text-gray-800">Translado</span>
        </label>

        <label class="inline-flex items-center gap-2">
            <input type="checkbox" wire:model.live="filtroMembro"
                   class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
            <span class="text-sm text-gray-800">Membro</span>
        </label>
    </div>
    <p class="mt-1 text-[11px] text-gray-500">
        Você pode marcar mais de uma opção. A busca trará registros que sejam <strong>qualquer um</strong> dos selecionados.
    </p>
</div>
</div>

</div>
