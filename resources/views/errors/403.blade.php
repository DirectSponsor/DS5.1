@extends('layouts.cpanelblank')
@section('content')
    <h1> You are not authorised for this action ! </h1>
    <p class="error">Check your permissions with the Project Coordinator !!</p>
    @if(isset($exception)
        <p class="error">{!! $exception->getMessage() !!}</p>
    @endif
@endsection
