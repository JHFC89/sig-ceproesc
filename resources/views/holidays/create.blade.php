@extends('layouts.dashboard')

@section('title', 'Cadastrar Feriados')

@push('head')
    @livewireStyles
@endpush

@section('content')
    <livewire:holiday-form />
@endsection

@push('footer')
    @livewireScripts
@endpush
