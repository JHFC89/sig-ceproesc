@extends('dashboard')

@section('title', 'Aulas De Hoje')

@section('content')
    <x-lesson.for-today-list title="Hoje" :user="request()->user()"/>
@endsection
