@extends('layouts.empresa')

@section('title', 'Editar Causa de Morte')

@section('page-content')
    <div class="bg-white shadow rounded-lg p-4">
        @livewire('empresa.causas-morte.form', ['causaMorteId' => $id])
    </div>
@endsection
