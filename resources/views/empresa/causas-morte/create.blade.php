@extends('layouts.empresa')

@section('title', 'Nova Causa de Morte')

@section('page-content')
    <div class="bg-white shadow rounded-lg p-4">
        @livewire('empresa.causas-morte.form')
    </div>
@endsection
