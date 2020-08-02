@extends('layouts.layout')

@section("content")
<categorydetailcomponent :slider_data="{{ json_encode($slider_data) }}"></categorydetailcomponent>
@endsection