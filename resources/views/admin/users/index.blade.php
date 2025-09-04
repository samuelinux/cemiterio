@extends('layouts.admin')

@section('title', 'Usuários')

@section('page-content')
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-900">Usuários</h2>

        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition">
           Novo Utilizador
        </a>
    </div>

    @livewire('admin.user-index')
@endsection
