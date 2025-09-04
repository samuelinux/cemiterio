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
        const T = Swal.mixin({
            toast: true, position: 'top-end',
            showConfirmButton: false, timer: 3500, timerProgressBar: true
        });
        T.fire({
            icon: $event.detail.type || 'info',
            title: $event.detail.title || ''
        })
    ">
    <form wire:submit.prevent="save" class="space-y-6">

        {{-- Nome --}}
        <div>
            <label for="nome" class="block text-sm font-medium text-gray-700">Nome da Empresa *</label>
            <input type="text" id="nome" wire:model.defer="nome"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('nome') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" wire:model.defer="email"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Telefone --}}
        <div>
            <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
            <input type="text" id="telefone" wire:model.defer="telefone"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('telefone') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Endereço --}}
        <div>
            <label for="endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
            <input type="text" id="endereco" wire:model.defer="endereco"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('endereco') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Cidade / Estado --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
                <input type="text" id="cidade" wire:model.defer="cidade"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                @error('cidade') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <input type="text" id="estado" wire:model.defer="estado" maxlength="2"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500 uppercase">
                @error('estado') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- CEP / CNPJ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="cep" class="block text-sm font-medium text-gray-700">CEP</label>
                <input type="text" id="cep" wire:model.defer="cep"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                @error('cep') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="cnpj" class="block text-sm font-medium text-gray-700">CNPJ</label>
                <input type="text" id="cnpj" wire:model.defer="cnpj"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                @error('cnpj') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Status --}}
        <div class="flex items-center space-x-2">
            <input type="checkbox" id="ativo" wire:model="ativo"
                   class="h-4 w-4 text-gray-600 border-gray-300 rounded">
            <label for="ativo" class="text-sm text-gray-700">Ativa</label>
        </div>

        {{-- Botão --}}
        <div class="flex justify-end">
            <button type="submit"
                    class="px-5 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition">
                {{ $isEditing ? 'Atualizar Empresa' : 'Salvar Empresa' }}
            </button>
        </div>
    </form>
</div>
