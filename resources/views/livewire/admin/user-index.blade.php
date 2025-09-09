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
        const T = Swal.mixin({toast:true, position:'top-end', showConfirmButton:false, timer:4000, timerProgressBar:true});
        T.fire({ icon: $event.detail.type || 'info', title: $event.detail.title || '' })
    "
    @swal-confirm-delete.window="
        Swal.fire({
            title: $event.detail.title,
            text: $event.detail.text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((r)=>{ if(r.isConfirmed){ $wire.delete($event.detail.id) } })
    "
>
    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="md:col-span-2">
            <input type="text" placeholder="Pesquisar por nome ou e-mail..." wire:model.live="search"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
        </div>

        <div>
            <select wire:model.live.number="perPage"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                <option value="10">10 por página</option>
                <option value="25">25 por página</option>
                <option value="50">50 por página</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $user->id }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $user->email }}</td>
                        <td class="px-4 py-2 text-sm">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                {{ $user->tipo_usuario === 'admin' ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-800' }}">
                                {{ $user->tipo_usuario }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs
                                {{ $user->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right space-x-2">
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                               class="px-3 py-1 rounded-md text-sm bg-gray-100 hover:bg-gray-200 text-gray-800">
                                Editar
                            </a>

                            <button type="button"
                                wire:click="confirmDelete({{ $user->id }})"
                                class="px-3 py-1 rounded-md text-sm bg-red-600 hover:bg-red-700 text-white">
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-sm text-gray-500" colspan="6">
                            Nenhum usuário encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
