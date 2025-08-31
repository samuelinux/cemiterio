<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Gestão de Empresas</h2>
        
        <!-- Filtros e Pesquisa -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pesquisar</label>
                    <input type="text" wire:model.live="search" placeholder="Nome, email ou CNPJ..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select wire:model.live="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <option value="">Todos</option>
                        <option value="1">Ativas</option>
                        <option value="0">Inativas</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button wire:click="clearFilters" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                        Limpar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Botão Nova Empresa -->
        <div class="mb-4">
            <a href="{{ route('admin.empresas.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nova Empresa
            </a>
        </div>
    </div>

    <!-- Tabela de Empresas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('nome')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Nome
                            @if($sortField === 'nome')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CNPJ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th wire:click="sortBy('created_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Criada em
                            @if($sortField === 'created_at')
                                <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($empresas as $empresa)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $empresa->nome }}</div>
                                <div class="text-sm text-gray-500">{{ $empresa->slug }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $empresa->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $empresa->cnpj }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($empresa->ativo)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Ativa
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Inativa
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $empresa->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.empresas.show', $empresa) }}" class="text-gray-600 hover:text-gray-900">
                                        Ver
                                    </a>
                                    <a href="{{ route('admin.empresas.edit', $empresa) }}" class="text-blue-600 hover:text-blue-900">
                                        Editar
                                    </a>
                                    <button wire:click="toggleStatus({{ $empresa->id }})" 
                                            class="text-{{ $empresa->ativo ? 'red' : 'green' }}-600 hover:text-{{ $empresa->ativo ? 'red' : 'green' }}-900">
                                        {{ $empresa->ativo ? 'Desativar' : 'Ativar' }}
                                    </button>
                                    <button wire:click="delete({{ $empresa->id }})" 
                                            wire:confirm="Tem certeza que deseja excluir esta empresa?"
                                            class="text-red-600 hover:text-red-900">
                                        Excluir
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Nenhuma empresa encontrada</p>
                                    <p class="text-sm">Comece criando uma nova empresa.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($empresas->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $empresas->links() }}
            </div>
        @endif
    </div>
</div>
