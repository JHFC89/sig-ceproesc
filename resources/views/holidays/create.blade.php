@extends('layouts.dashboard')

@section('title', 'Cadastrar Feriados')

@section('head')
    @livewireStyles
@endsection

@section('content')
    <livewire:holiday-form />
@endsection

@section('scripts')
    @livewireScripts
@endsection
