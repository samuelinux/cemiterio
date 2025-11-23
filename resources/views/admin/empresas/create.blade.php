@extends('layouts.admin')

@section('title', 'Nova Empresa')

@section('page-content')
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Cadastrar Nova Empresa</h2>

        {{-- Componente Livewire já existente --}}
        @livewire('admin.empresa-form')
    </div>
@endsection
