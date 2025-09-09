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
    class="space-y-4"
>
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <h2 class="text-lg font-semibold text-gray-900">Sepultamentos</h2>

        <a href="{{ route('empresa.sepultamentos.create', Auth::user()->empresa->slug) }}"
           class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800 transition text-center">
            Novo Sepultamento
        </a>
    </div>

    {{-- Filtros --}}
    <div class="flex flex-col md:flex-row md:items-center gap-2">
        <input type="text"
               placeholder="Buscar por nome, quadra, fila, cova..."
               wire:model.live.debounce.300ms="search"
               class="w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">

        <select wire:model.live.number="perPage"
                class="rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            <option value="10">10 por página</option>
            <option value="25">25 por página</option>
            <option value="50">50 por página</option>
        </select>

        <select wire:model.live="status"
                class="rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
            <option value="">Todos</option>
            <option value="ativo">Apenas ativos</option>
            <option value="inativo">Apenas inativos</option>
        </select>
    </div>

    {{-- Tabela (desktop) --}}
    <div class="overflow-x-auto hidden md:block">
        <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg shadow">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Falecimento</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sepultamento</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Q / F / C</th>
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
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $s->data_falecimento?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $s->data_sepultamento?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600">
                            {{ $s->quadra ?? '-' }} / {{ $s->fila ?? '-' }} / {{ $s->cova ?? '-' }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('empresa.sepultamentos.edit', [Auth::user()->empresa->slug, $s->id]) }}"
                                   class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition">
                                    Editar
                                </a>
                                <button type="button"
                                        wire:click="confirmDelete({{ $s->id }})"
                                        class="px-3 py-1.5 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition">
                                    Excluir
                                </button>
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

    {{-- Cards (mobile) --}}
    <div class="space-y-2 md:hidden">
        @forelse($sepultamentos as $s)
            <div class="border rounded-lg p-3 shadow-sm bg-white">
                <div class="font-medium text-gray-900">{{ $s->nome_falecido }}</div>
                <div class="text-xs text-gray-500">{{ $s->numeroFormatado() }}</div>

                <div class="mt-1 text-sm text-gray-600">
                    <div>Falecimento: {{ $s->data_falecimento?->format('d/m/Y') ?? '-' }}</div>
                    <div>Sepultamento: {{ $s->data_sepultamento?->format('d/m/Y') ?? '-' }}</div>
                    <div>Local: {{ $s->quadra ?? '-' }} / {{ $s->fila ?? '-' }} / {{ $s->cova ?? '-' }}</div>
                </div>

                <div class="mt-3 flex justify-end gap-2">
                    <a href="{{ route('empresa.sepultamentos.edit', [Auth::user()->empresa->slug, $s->id]) }}"
                       class="px-3 py-1.5 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition">
                        Editar
                    </a>
                    <button type="button"
                            wire:click="confirmDelete({{ $s->id }})"
                            class="px-3 py-1.5 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition">
                        Excluir
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center text-sm text-gray-500">Nenhum sepultamento encontrado.</div>
        @endforelse
    </div>

    {{-- Paginação --}}
    <div>
        @if ($sepultamentos->count())
    {{-- tabela/listagem --}}
    {{ $sepultamentos->links() }}
@else
    <p class="text-center text-gray-500 py-6">
        Você não tem permissão para listar sepultamentos.
    </p>
@endif

    </div>
</div>
