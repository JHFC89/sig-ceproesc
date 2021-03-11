@extends('layouts.dashboard')

@section('title', 'Cadastrar Turma')

@push('head')
    @livewireStyles
@endpush

@section('content')
    <livewire:course-class-form>
@endsection

@push('footer')
    @livewireScripts
@endpush
