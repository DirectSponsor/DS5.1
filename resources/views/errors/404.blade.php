@extends('layouts.cpanelblank')
@section('content')
    <p class="error">NOT FOUND :: Apologies - Looks like we are having problems finding pages, or data</p>
    <p class="error">{!! $exception->getMessage() !!}</p>
@endsection
