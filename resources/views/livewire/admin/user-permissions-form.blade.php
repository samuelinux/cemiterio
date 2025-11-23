{{-- resources/views/livewire/admin/user-permissions-form.blade.php --}}
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
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Permissões do Usuário</h3>

    

    {{-- Lista de tabelas e permissões --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg shadow">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Usar</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tabela</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Consultar</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Cadastrar</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Editar</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Excluir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tableList as $t)
                    <tr>
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox"
                                   wire:model.live="rows.{{ $t }}.selected"
                                   class="h-4 w-4 text-gray-600 border-gray-300 rounded">
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-900 font-medium">
                            <code>{{ $t }}</code>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox"
                                   wire:model.live="rows.{{ $t }}.consultar"
                                   @disabled(!$rows[$t]['selected'])
                                   class="h-4 w-4 text-gray-600 border-gray-300 rounded">
                        </td>
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox"
                                   wire:model.live="rows.{{ $t }}.cadastrar"
                                   @disabled(!$rows[$t]['selected'])
                                   class="h-4 w-4 text-gray-600 border-gray-300 rounded">
                        </td>
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox"
                                   wire:model.live="rows.{{ $t }}.editar"
                                   @disabled(!$rows[$t]['selected'])
                                   class="h-4 w-4 text-gray-600 border-gray-300 rounded">
                        </td>
                        <td class="px-4 py-2 text-center">
                            <input type="checkbox"
                                   wire:model.live="rows.{{ $t }}.excluir"
                                   @disabled(!$rows[$t]['selected'])
                                   class="h-4 w-4 text-gray-600 border-gray-300 rounded">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">
                            Nenhuma tabela encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex justify-end mt-4">
        <button type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
                class="px-5 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition inline-flex items-center gap-2">
            <span wire:loading.remove wire:target="save">Salvar Permissões</span>
            <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                    <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" class="opacity-75"/>
                </svg>
                Salvando...
            </span>
        </button>
    </div>
</div>
