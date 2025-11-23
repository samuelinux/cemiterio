@extends('layouts.admin')

@section('title', 'Empresas')

@section('page-content')
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-900">Empresas</h2>

        <a href="{{ route('admin.empresas.create') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition">
           Nova Empresa
        </a>
    </div>

    @livewire('admin.empresa-index')
@endsection
