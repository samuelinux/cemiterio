@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')

@section('page-content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Empresas -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Empresas</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $totalEmpresas }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-green-600 font-medium">{{ $empresasAtivas }}</span>
                <span class="text-gray-500"> ativas</span>
            </div>
        </div>
    </div>

    <!-- Total Utilizadores -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Utilizadores</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $totalUsuarios }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-5 py-3">
            <div class="text-sm">
                <span class="text-green-600 font-medium">{{ $usuariosAtivos }}</span>
                <span class="text-gray-500"> ativos</span>
            </div>
        </div>
    </div>

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
                <span class="text-blue-600 font-medium">{{ $sepultamentosHoje }}</span>
                <span class="text-gray-500"> hoje</span>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ações Rápidas</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.empresas.create') }}" 
                   class="block w-full bg-gray-800 text-white text-center py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-700 transition-colors">
                    Nova Empresa
                </a>
                <a href="{{ route('admin.users.create') }}" 
                   class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                    Novo Utilizador
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Resumo das Atividades -->
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            Resumo do Sistema
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Empresas por Status</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Ativas</span>
                        <span class="text-sm font-medium text-green-600">{{ $empresasAtivas }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Inativas</span>
                        <span class="text-sm font-medium text-red-600">{{ $totalEmpresas - $empresasAtivas }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Utilizadores por Status</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Ativos</span>
                        <span class="text-sm font-medium text-green-600">{{ $usuariosAtivos }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Inativos</span>
                        <span class="text-sm font-medium text-red-600">{{ $totalUsuarios - $usuariosAtivos }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

