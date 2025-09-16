@extends('layouts.empresa')

@section('title', 'Sepultamentos')

@section('page-content')
    <div class="bg-white shadow rounded-lg p-4">
        @livewire('empresa.sepultamento.index')
    </div>
@endsection
