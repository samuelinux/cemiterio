<div
    x-data="{ showCreate: @entangle('showCreateModal'), showEdit: @entangle('showEditModal') }"
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
    @swal-confirm-delete.window="
        Swal.fire({
            icon: 'warning',
            title: $event.detail.title || 'Excluir?',
            text: $event.detail.text || 'Confirma a exclusão?',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        }).then((r) => { if (r.isConfirmed) { $wire.delete($event.detail.id) } })
    "
    class="space-y-4">
    {{-- Barra de ações --}}
    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
        <div class="flex gap-2 items-center">
            <input type="text"
                placeholder="Buscar..."
                wire:model.live="search"
                class="w-full sm:w-64 border rounded-md p-2 focus:ring-gray-500 focus:border-gray-500">

            <select wire:model.live="perPage"
                class="border rounded-md p-2">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>

        <button type="button"
            @click="showCreate = true; $wire.create()"
            class="self-start sm:self-auto inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            Nova Causa
        </button>
    </div>

    {{-- Tabela --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">CID-10</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Ativo</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($causas as $c)
                <tr>
                    <td class="px-3 py-2 text-sm text-gray-900">{{ $c->descricao }}</td>
                    <td class="px-3 py-2 text-sm text-gray-700">{{ $c->codigo_cid10 }}</td>
                    <td class="px-3 py-2 text-center">
                        <span class="px-2 py-1 rounded text-xs {{ $c->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $c->ativo ? 'Sim' : 'Não' }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <div class="flex justify-center gap-2">
                            <button type="button"
                                @click="showEdit = true; $wire.edit({{ $c->id }})"
                                class="px-3 py-1 rounded bg-yellow-500 text-white hover:bg-yellow-600">
                                Editar
                            </button>
                            <button type="button"
                                @click="$dispatch('swal-confirm-delete', { id: {{ $c->id }},
                                        title: 'Excluir causa?',
                                        text: 'Confirmar excluir a causa: {{ addslashes($c->descricao) }}?' })"
                                class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700">
                                Excluir
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-3 py-6 text-center text-sm text-gray-500">Nenhuma causa encontrada.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginação --}}
    <div>{{ $causas->links() }}</div>

    {{-- MODAL: Criar --}}
    <div x-show="showCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showCreate = false; $wire.closeModals()"></div>
        <div class="relative bg-white w-full max-w-5xl mx-4 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Nova Causa de Morte</h3>

            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descrição *</label>

                    <textarea
                        rows="6"
                        maxlength="2500"
                        wire:model.blur="descricao"
                        placeholder="Digite a descrição completa..."
                        class="mt-1 block w-full border rounded-md p-4 focus:ring-gray-500 focus:border-gray-500 resize-none"></textarea>

                    @error('descricao')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Código CID-10</label>
                    <input type="text" wire:model.blur="codigo_cid10" maxlength="200"
                        class="mt-1 block w-full border rounded-md py-2 focus:ring-gray-500 focus:border-gray-500">
                    @error('codigo_cid10') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="ativo_create" wire:model="ativo"
                        class="h-4 w-4 text-gray-600 border-gray-300 rounded">
                    <label for="ativo_create" class="text-sm text-gray-700">Ativo</label>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                        @click="showCreate = false; $wire.closeModals()"
                        class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Editar --}}
    <div x-show="showEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showEdit = false; $wire.closeModals()"></div>
        <div class="relative bg-white w-full max-w-5xl mx-4 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Causa de Morte</h3>

            <form wire:submit.prevent="update" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descrição *</label>
                    <textarea
                        rows="6"
                        maxlength="2500"
                        wire:model.blur="descricao"
                        placeholder="Digite a descrição completa..."
                        class="mt-1 block w-full border rounded-md p-4 focus:ring-gray-500 focus:border-gray-500 resize-none"></textarea>
                    @error('descricao') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Código CID-10</label>
                    <input type="text" wire:model.blur="codigo_cid10"
                        class="mt-1 block w-full border rounded-md p-2 focus:ring-gray-500 focus:border-gray-500">
                    @error('codigo_cid10') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="ativo_edit" wire:model="ativo"
                        class="h-4 w-4 text-gray-600 border-gray-300 rounded">
                    <label for="ativo_edit" class="text-sm text-gray-700">Ativo</label>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button"
                        @click="showEdit = false; $wire.closeModals()"
                        class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>