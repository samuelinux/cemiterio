<div x-data="{ showCreate: @entangle('showCreateModal'), showEdit: @entangle('showEditModal') }"
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
    @redirect-to-download.window="
    console.log('Redirecionando para visualização em nova guia', $event.detail);
    try {
        window.open($event.detail.url, '_blank'); // Alterado de window.location.href para window.open com '_blank' para nova guia
    } catch (e) {
        console.error('Erro ao abrir PDF em nova guia:', e);
        Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: 'Falha ao iniciar a visualização do PDF. Tente novamente.'
        });
    }
"
    class="space-y-4">
    <!-- Resto do código permanece inalterado -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h2 class="text-lg font-semibold text-gray-900">Sepultamentos</h2>

        @if ($canCreate)
            <button type="button" @click="showCreate = true; $wire.create()"
                class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition text-center">
                Novo Sepultamento
            </button>
        @endif
    </div>

    @include('livewire.empresa.sepultamento.search-fields')

    <div class="overflow-x-auto hidden md:block">
        <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg shadow">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Falecimento</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sepultamento</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Q / F / C / OS</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sepultamentos as $s)
                    <tr>
                        <td class="px-3 py-2 text-sm text-gray-900">
                            <div class="font-medium">{{ $s->nome_falecido }}</div>
                            <div class="text-xs text-gray-500">{{ $s->numeroFormatado() }}</div>
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-600">
                            {{ $s->data_falecimento?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-600">
                            {{ $s->data_sepultamento?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-600">
                            {{ $s->quadra ?? '-' }} / {{ $s->fila ?? '-' }} / {{ $s->cova ?? '-' }} / {{ $s->ordem_sepultamento }}
                        </td>
                        
                        <td class="px-3 py-2 text-right">
                            <div class="inline-flex items-center gap-2">
                                @if ($s->certidao_obito_path)
                                    <a href="{{ Storage::url($s->certidao_obito_path) }}" target="_blank"
                                        class="px-3 py-1.5 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="size-6">
                                            <path
                                                d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625Z" />
                                            <path
                                                d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                                        </svg>

                                    </a>
                                @endif
                                @if ($canEdit)
                                    <button type="button" @click="showEdit = true; $wire.edit({{ $s->id }})"
                                        class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition">
                                        Editar
                                    </button>
                                @endif
                                @if ($canDelete)
                                    <button type="button" wire:click="confirmDelete({{ $s->id }})"
                                        class="px-3 py-1.5 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition">
                                        Excluir
                                    </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-gray-500">
                            Nenhum sepultamento encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="space-y-2 md:hidden">
        @forelse($sepultamentos as $s)
            <div class="border rounded-lg p-3 shadow-sm bg-white">
                <div class="font-medium text-gray-900">{{ $s->nome_falecido }}</div>
                <div class="text-xs text-gray-500">{{ $s->numeroFormatado() }}</div>

                <div class="mt-1 text-sm text-gray-600">
                    <div>Falecimento: {{ $s->data_falecimento?->format('d/m/Y') ?? '-' }}</div>
                    <div>Sepultamento: {{ $s->data_sepultamento?->format('d/m/Y') ?? '-' }}</div>
                    <div>Q: {{ $s->quadra ?? '-' }}/ F: {{ $s->fila ?? '-' }}/ C: {{ $s->cova ?? '-' }}/ N°S: {{ $s->ordem_sepultamento ?? '-'  }}</div>
                </div>

                <div class="mt-3 flex justify-end gap-2">
                    @if ($s->certidao_obito_path)
                        <a href="{{ Storage::url($s->certidao_obito_path) }}" target="_blank"
                            class="px-3 py-1.5 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="size-6">
                                <path
                                    d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625Z" />
                                <path
                                    d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                            </svg>

                        </a>
                    @endif
                    @if ($canEdit)
                        <button type="button" @click="showEdit = true; $wire.edit({{ $s->id }})"
                            class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition">
                            Editar
                        </button>
                    @endif
                    @if ($canDelete)
                        <button type="button" wire:click="confirmDelete({{ $s->id }})"
                            class="px-3 py-1.5 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition">
                            Excluir
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-sm text-gray-500">Nenhum sepultamento encontrado.</div>
        @endforelse
    </div>

    <div>{{ $sepultamentos->links() }}</div>

    <div x-show="showCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showCreate = false; $wire.closeModals()"></div>
        <div class="relative bg-white w-full max-w-2xl mx-4 rounded-xl shadow p-6 flex flex-col">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Novo Sepultamento</h3>

            <form wire:submit.prevent="store" class="flex flex-col flex-1">
                <div class="flex-1 overflow-y-auto pr-1">
                    @include('livewire.empresa.sepultamento.form-fields')
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t mt-4">
                    <button type="button" @click="showCreate = false; $wire.closeModals()"
                        class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="showEdit = false; $wire.closeModals()"></div>
        <div class="relative bg-white w-full max-w-2xl mx-4 rounded-xl shadow p-6 flex flex-col">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Editar Sepultamento</h3>

            <form wire:submit.prevent="update" class="flex flex-col flex-1">
                <div class="flex-1 overflow-y-auto pr-1">
                    @include('livewire.empresa.sepultamento.form-fields')
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t mt-4">
                    <button type="button" @click="showEdit = false; $wire.closeModals()"
                        class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
