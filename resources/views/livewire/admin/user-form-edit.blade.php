{{-- resources/views/livewire/admin/user-form-edit.blade.php --}}
<div
    x-data
    @toast.window="
        const T = Swal.mixin({toast:true, position:'top-end', showConfirmButton:false, timer:4000, timerProgressBar:true});
        T.fire({ icon: $event.detail.type || 'info', title: $event.detail.title || '' })
    "
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
    class="bg-white shadow rounded-lg p-6"
>
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Dados do Usuário</h3>

    <form wire:submit.prevent="save" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700">Nome *</label>
            <input type="text" wire:model.blur="name"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">E-mail *</label>
            <input type="email" wire:model.blur="email"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Nova senha (opcional)</label>
            <input type="password" wire:model.defer="password"
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                   placeholder="Deixe em branco para manter a atual">
            @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Empresa</label>
            <select wire:model.blur="empresa_id"
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                <option value="">-- Nenhuma --</option>
                @foreach($empresas as $empresa)
                    <option value="{{ $empresa->id }}">{{ $empresa->nome }}</option>
                @endforeach
            </select>
            @error('empresa_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de Usuário *</label>
                <select wire:model.live="tipo_usuario"
                        class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                    <option value="admin">Admin</option>
                    <option value="user">Usuário</option>
                </select>
                @error('tipo_usuario') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center mt-7">
                <input id="ativo" type="checkbox" wire:model.live="ativo"
                       class="h-4 w-4 text-gray-600 border-gray-300 rounded">
                <label for="ativo" class="ml-2 text-sm text-gray-700">Ativo</label>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="px-5 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition inline-flex items-center gap-2">
                <span wire:loading.remove wire:target="save">Salvar alterações</span>
                <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                        <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" class="opacity-75"/>
                    </svg>
                    Salvando...
                </span>
            </button>
        </div>
    </form>
</div>
