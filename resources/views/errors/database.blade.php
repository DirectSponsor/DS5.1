@extends('layouts.cpanelblank')
@section('content')
    <h1> Apologies : We have a database error. ! </h1>
    <p class="error">Please inform the Project Coordinator or Administrator ?</p>
    @if(isset($exceptionMessage))
        <p class="error">{!! $exceptionMessage !!}</p>
    @endif
@endsection
