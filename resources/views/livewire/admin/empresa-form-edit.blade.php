<div
    x-data
    @swal.window="
        Swal.fire({
            icon: $event.detail.type || 'info',
            title: $event.detail.title || 'Atenção',
            text: $event.detail.text || undefined,
            html: $event.detail.html || undefined,
            showConfirmButton: $event.detail.showConfirmButton ?? true,
            timer: $event.detail.timer || undefined,
            timerProgressBar: !!$event.detail.timer
        })
    "
    @toast.window="
        const T = Swal.mixin({toast:true, position:'top-end', showConfirmButton:false, timer:2500, timerProgressBar:true});
        T.fire({ icon: $event.detail.type || 'info', title: $event.detail.title || '' })
    "
>
    <form wire:submit.prevent="save" class="space-y-6">
        {{-- Nome --}}
        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700">Nome da Empresa *</label>
            <input type="text" id="nome" wire:model.blur="nome"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('nome') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Slug (editável) + botão gerar --}}
        <div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug *</label>
                    <input type="text" id="slug" wire:model.live.defer="slug"
                           placeholder="ex.: minha-empresa"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                    @error('slug') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">
                        Deve ser único. Se já existir, adicionaremos um sufixo <em>-1, -2</em>…
                    </p>
                </div>

                <button type="button"
                        wire:click="suggestSlug"
                        class="px-3 py-2 h-10 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 transition">
                    Gerar do nome
                </button>
            </div>
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" wire:model.blur="email"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Telefone --}}
        <div>
            <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
            <input type="text" id="telefone" wire:model.blur="telefone"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('telefone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Endereço --}}
        <div>
            <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
            <input type="text" id="endereco" wire:model.blur="endereco"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('endereco') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Cidade / Estado --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
                <input type="text" id="cidade" wire:model.blur="cidade"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                @error('cidade') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <input type="text" id="estado" wire:model.blur="estado" maxlength="2"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 uppercase">
                @error('estado') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- CEP / CNPJ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="cep" class="block text-sm font-medium text-gray-700">CEP</label>
                <input type="text" id="cep" wire:model.blur="cep"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                @error('cep') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
                <input type="text" id="cnpj" wire:model.blur="cnpj"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                @error('cnpj') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Status (ativação/inativação) --}}
        <div class="flex items-center space-x-2">
            <input type="checkbox" id="ativo" wire:model="ativo"
                   class="h-4 w-4 text-gray-600 border-gray-300 rounded">
            <label for="ativo" class="text-sm text-gray-700">Ativa</label>
        </div>

        {{-- Botões --}}
        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.empresas.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 transition">
               Cancelar
            </a>

            <button type="submit"
                    class="px-5 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition">
                Atualizar Empresa
            </button>
        </div>
    </form>
</div>
