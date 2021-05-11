@extends('layouts.dashboard')

@section('title', 'Início')
@section('content')
    <x-dashboard.coordinator :show="request()->user()->isCoordinator()"/> 
    <x-dashboard.instructor :show="request()->user()->isInstructor()"/> 
    <x-dashboard.novice :show="request()->user()->isNovice()"/> 
    <x-dashboard.employer :show="request()->user()->isEmployer()"/> 
@endsection
