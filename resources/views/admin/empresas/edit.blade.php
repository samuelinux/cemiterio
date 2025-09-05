@extends('layouts.admin')

@section('title', 'Editar Empresa')

@section('page-content')
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Editar Empresa</h2>

        {{-- Mesmo componente, agora em modo edição --}}
        @livewire('admin.empresa-form-edit', ['empresaId' => $empresa->id])
    </div>
@endsection
