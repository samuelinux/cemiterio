@extends('layouts.empresa')

@section('title', 'Dashboard - ' . $empresa->nome)

@section('page-content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Sepultamentos -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Sepultamentos</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $totalSepultamentos }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <a href="{{ route('empresa.sepultamentos.index', $empresa->slug) }}" class="text-blue-600 hover:text-blue-500">
                    Ver todos →
                </a>
            </div>
        </div>
    </div>

    <!-- Sepultamentos Hoje -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Hoje</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $sepultamentosHoje }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-gray-500">{{ now()->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Sepultamentos Este Mês -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Este Mês</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $sepultamentosEsteMes }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-gray-500">{{ now()->format('M/Y') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Ação Rápida -->
<div class="mb-8">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-blue-900">Registar Novo Sepultamento</h3>
                <p class="text-blue-700 mt-1">Adicione um novo registo de sepultamento ao sistema.</p>
            </div>
            <div>
                <a href="{{ route('empresa.sepultamentos.create', $empresa->slug) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                    Novo Sepultamento
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Últimos Sepultamentos -->
@if($ultimosSepultamentos->count() > 0)
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            Últimos Sepultamentos
        </h3>
        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Falecido
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data Sepultamento
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Local
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Registado por
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($ultimosSepultamentos as $sepultamento)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $sepultamento->nome_falecido }}</div>
                            @if($sepultamento->cpf_falecido)
                                <div class="text-sm text-gray-500">CPF: {{ $sepultamento->cpf_falecido }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $sepultamento->data_sepultamento->format('d/m/Y') }}
                            @if($sepultamento->hora_sepultamento)
                                <br><span class="text-gray-500">{{ $sepultamento->hora_sepultamento->format('H:i') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $sepultamento->local_sepultamento }}</div>
                            @if($sepultamento->numero_sepultura)
                                <div class="text-sm text-gray-500">Sepultura: {{ $sepultamento->numero_sepultura }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $sepultamento->user->name }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <a href="{{ route('empresa.sepultamentos.index', $empresa->slug) }}" 
               class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                Ver todos os sepultamentos →
            </a>
        </div>
    </div>
</div>
@else
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum sepultamento registado</h3>
        <p class="mt-1 text-sm text-gray-500">Comece registando o primeiro sepultamento.</p>
        <div class="mt-6">
            <a href="{{ route('empresa.sepultamentos.create', $empresa->slug) }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Novo Sepultamento
            </a>
        </div>
    </div>
</div>
@endif
@endsection

