@extends('layouts.admin')

@section('title', 'Novo Usuário')

@section('page-content')
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Cadastrar Novo Usuário</h2>

        {{-- Componente Livewire --}}
        @livewire('admin.user-form')
    </div>
@endsection
