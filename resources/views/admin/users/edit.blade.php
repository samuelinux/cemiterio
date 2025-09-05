{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Editar Usuário')

@section('page-content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Editar Usuário</h2>
        <a href="{{ route('admin.users.index') }}"
           class="px-3 py-2 rounded-md text-sm bg-gray-100 hover:bg-gray-200 text-gray-800">
           Voltar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Dados do usuário --}}
        @livewire('admin.user-form-edit', ['userId' => $user->id])
        {{-- Permissões por tabela --}}
        @livewire('admin.user-permissions-form', ['userId' => $user->id])
    </div>
@endsection
