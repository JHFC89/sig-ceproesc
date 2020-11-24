@extends('dashboard')

@section('title', 'Aulas De Hoje')

@section('content')
    <x-lesson.for-week-list title="Esta Semana" :user="request()->user()"/>
@endsection
