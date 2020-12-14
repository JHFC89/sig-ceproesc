@extends('layouts.dashboard')

@section('title', 'Início')
@section('content')
<div class="flex space-x-8">
    <div class="w-1/2">
        <x-lesson.for-today-list title="aulas de hoje" :hideRegistered="true" :user="request()->user()"/>
    </div>
</div>
@endsection
