@extends('layouts.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section("content")
    <searchcomponent></searchcomponent>
@endsection