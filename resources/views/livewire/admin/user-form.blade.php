<div x-data
    @toast.window="Swal.fire({toast:true, position:'top-end', icon:$event.detail.type, title:$event.detail.title, showConfirmButton:false, timer:2500})">
    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700">Nome *</label>
            <input type="text" wire:model.defer="name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('name')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email *</label>
            <input type="email" wire:model.defer="email"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('email')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Senha *</label>
            <input type="password" wire:model.defer="password"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('password')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Empresa</label>
            <select wire:model.defer="empresa_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                <option value="">-- Nenhuma --</option>
                @foreach ($empresas as $empresa)
                    <option value="{{ $empresa->id }}">{{ $empresa->nome }}</option>
                @endforeach
            </select>
            @error('empresa_id')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo de Usuário *</label>
            <select wire:model.defer="tipo_usuario"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                <option value="admin">Admin</option>
                <option value="user">Usuário</option>
            </select>
            @error('tipo_usuario')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex items-center space-x-2">
            <input type="checkbox" id="ativo" wire:model="ativo"
                class="h-4 w-4 text-gray-600 border-gray-300 rounded">
            <label for="ativo" class="text-sm text-gray-700">Ativo</label>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition">
                Salvar Usuário
            </button>
        </div>
    </form>
</div>
