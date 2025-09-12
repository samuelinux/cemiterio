{{-- resources/views/livewire/empresa/partials/_sepultamento-form-fields.blade.php --}}
<div class="max-h-[calc(100vh-200px)] overflow-y-auto">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- Nome do falecido --}}
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Nome do falecido *</label>
            <input type="text" wire:model.blur="nome_falecido"
                   placeholder="Ex: João da Silva"
                   class="mt-1 w-full rounded-md bg-white border border-gray-300 shadow-sm
                          focus:border-gray-500 focus:ring focus:ring-gray-200
                          px-3 py-2 text-sm text-gray-900 placeholder-gray-400 transition">
            @error('nome_falecido') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Mãe --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Mãe</label>
            <input type="text" wire:model.blur="mae"
                   placeholder="Nome da mãe"
                   class="mt-1 w-full rounded-md bg-white border border-gray-300 shadow-sm
                          focus:border-gray-500 focus:ring focus:ring-gray-200
                          px-3 py-2 text-sm text-gray-900 placeholder-gray-400 transition">
            @error('mae') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Pai --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Pai</label>
            <input type="text" wire:model.blur="pai"
                   placeholder="Nome do pai"
                   class="mt-1 w-full rounded-md bg-white border border-gray-300 shadow-sm
                          focus:border-gray-500 focus:ring focus:ring-gray-200
                          px-3 py-2 text-sm text-gray-900 placeholder-gray-400 transition">
            @error('pai') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Datas --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Data de falecimento</label>
            <input type="date" wire:model.live="data_falecimento"
                   class="mt-1 w-full rounded-md bg-white border border-gray-300 shadow-sm
                          focus:border-gray-500 focus:ring focus:ring-gray-200
                          px-3 py-2 text-sm text-gray-900 transition">
            @error('data_falecimento') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Data de sepultamento</label>
            <input type="date" wire:model.live="data_sepultamento"
                   class="mt-1 w-full rounded-md bg-white border border-gray-300 shadow-sm
                          focus:border-gray-500 focus:ring focus:ring-gray-200
                          px-3 py-2 text-sm text-gray-900 transition">
            @error('data_sepultamento') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Localização --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Quadra</label>
            <input type="text" wire:model.blur="quadra"
                   placeholder="Ex: QD-12"
                   class="mt-1 w-full rounded-md bg-white border border-gray-300 shadow-sm
                          focus:border-gray-500 focus:ring focus:ring-gray-200
                          px-3 py-2 text-sm text-gray-900 placeholder-gray-400 transition">
            @error('quadra') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Fila</label>
            <input type="text" wire:model.blur="fila"
                   placeholder="Ex: F-03"
                   class="mt-1 w-full rounded-md bg-white border border-gray-300 shadow-sm
                          focus:border-gray-500 focus:ring focus:ring-gray-200
                          px-3 py-2 text-sm text-gray-900 placeholder-gray-400 transition">
            @error('fila') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Cova</label>
            <input type="text" wire:model.blur="cova"
                   placeholder="Ex: 27"
                   class="mt-1 w-full rounded-md bg-white border border-gray-300 shadow-sm
                          focus:border-gray-500 focus:ring focus:ring-gray-200
                          px-3 py-2 text-sm text-gray-900 placeholder-gray-400 transition">
            @error('cova') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Certidão de óbito (upload de arquivo PDF) --}}
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Certidão de óbito (PDF)</label>
            <input type="file" wire:model="certidao_obito"
                   accept="application/pdf"
                   class="mt-1 w-full rounded-md bg-white border border-gray-300 shadow-sm
                          focus:border-gray-500 focus:ring focus:ring-gray-200
                          px-3 py-2 text-sm text-gray-900 transition">
            @error('certidao_obito') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @if($certidao_obito_path)
                <div class="mt-2 text-sm text-gray-600">
                    Arquivo atual: <a href="{{ Storage::url($certidao_obito_path) }}" target="_blank" class="text-blue-600 hover:underline">Visualizar PDF</a>
                </div>
            @endif
        </div>

        {{-- Causas da morte (multi) – mobile-first com busca, chips e lista rolável --}}
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Causas da morte</label>
            <div
                x-data="{
                    all: @js($listaCausas),
                    query: '',
                    selecionadas: @entangle('causasSelecionadas'),
                    nfd(s) { return String(s).normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase() },
                    get total() { return this.all.length },
                    get filtradas() {
                        const q = this.nfd(this.query.trim());
                        if (!q) return this.all;
                        return this.all.filter(c => this.nfd(c.descricao).includes(q));
                    },
                    alternar(id) {
                        id = Number(id);
                        const i = this.selecionadas.indexOf(id);
                        if (i > -1) this.selecionadas.splice(i,1);
                        else this.selecionadas.push(id);
                    },
                    marcarTodas() { this.selecionadas = this.all.map(c => Number(c.id)); },
                    limparSelecao() { this.selecionadas = []; },
                    limparBusca() { this.query=''; }
                }"
                class="mt-1 rounded-md border border-gray-300 bg-white shadow-sm"
            >
                <div class="flex flex-col gap-2 p-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <input
                            x-model="query"
                            type="text"
                            placeholder="Buscar causa (descrição)"
                            class="w-full sm:w-72 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm
                                   placeholder-gray-400 focus:border-gray-500 focus:ring focus:ring-gray-200 transition"
                        />
                        <button type="button" @click="limparBusca()"
                                class="hidden sm:inline-flex rounded-md border px-2 py-2 text-xs text-gray-700 hover:bg-gray-50">
                            Limpar busca
                        </button>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs text-gray-600">
                            Selecionadas: <strong x-text="selecionadas.length"></strong>
                            <span class="text-gray-400">/</span>
                            <span x-text="total" class="text-gray-500"></span>
                        </span>
                        <button type="button" @click="marcarTodas()"
                                class="rounded-md border px-2 py-1.5 text-xs text-gray-700 hover:bg-gray-50">
                            Marcar todas
                        </button>
                        <button type="button" @click="limparSelecao()"
                                class="rounded-md border px-2 py-1.5 text-xs text-gray-700 hover:bg-gray-50">
                            Limpar seleção
                        </button>
                    </div>
                </div>
                <div class="px-3 pb-2">
                    <div class="flex gap-2 overflow-x-auto">
                        <template x-for="id in selecionadas" :key="id">
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-800">
                                <span x-text="(all.find(c => Number(c.id)===Number(id)) || {}).descricao ?? id"></span>
                                <button type="button" class="ml-1 text-gray-500 hover:text-gray-700"
                                        @click="alternar(id)">✕</button>
                            </span>
                        </template>
                        <template x-if="selecionadas.length===0">
                            <span class="text-xs text-gray-400">Nenhuma selecionada</span>
                        </template>
                    </div>
                </div>
                <div class="max-h-64 overflow-y-auto border-t border-gray-200">
                    <ul class="divide-y divide-gray-100">
                        <template x-for="c in filtradas" :key="c.id">
                            <li>
                                <label class="flex items-center gap-3 px-3 py-2.5 active:bg-gray-50">
                                    <input type="checkbox"
                                           :checked="selecionadas.includes(Number(c.id))"
                                           @change="alternar(c.id)"
                                           class="h-5 w-5 rounded border-gray-300 text-gray-700 focus:ring-gray-500">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-medium text-gray-900" x-text="c.descricao"></div>
                                        <div class="text-xs text-gray-500" x-text="'ID: ' + c.id"></div>
                                    </div>
                                </label>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
            @error('causasSelecionadas.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Observações --}}
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Observações</label>
            <textarea rows="3" wire:model.blur="observacoes"
                      placeholder="Digite observações relevantes..."
                      class="mt-1 w-full rounded-md bg-white border border-gray-300 shadow-sm
                             focus:border-gray-500 focus:ring focus:ring-gray-200
                             px-3 py-2 text-sm text-gray-900 placeholder-gray-400 transition"></textarea>
            @error('observacoes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Checkboxes de status --}}
        <div class="flex flex-wrap gap-4 sm:col-span-2">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model.live="indigente"
                       class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
                <span class="text-sm text-gray-800">Indigente</span>
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model.live="natimorto"
                       class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
                <span class="text-sm text-gray-800">Natimorto</span>
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model.live="translado"
                       class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
                <span class="text-sm text-gray-800">Translado</span>
            </label>
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" wire:model.live="membro"
                       class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
                <span class="text-sm text-gray-800">Membro</span>
            </label>
            <label class="inline-flex items-center gap-2 ml-auto">
                <input type="checkbox" wire:model.live="ativo"
                       class="h-4 w-4 rounded border-gray-300 text-gray-600 focus:ring-gray-500">
                <span class="text-sm text-gray-800">Ativo</span>
            </label>
        </div>
    </div>
</div>