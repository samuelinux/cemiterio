@extends('layouts.empresa')

@section('title', 'Causas Morte')

@section('page-content')
    <div class="bg-white shadow rounded-lg p-4">
        @livewire('empresa.causas-morte.index')
    </div>
@endsection
