@extends('layouts.dashboard')

@section('title', 'Início')
@section('content')
    <x-dashboard.novice :show="request()->user()->isNovice()"/> 
    <x-dashboard.instructor :show="request()->user()->isInstructor()"/> 
@endsection
