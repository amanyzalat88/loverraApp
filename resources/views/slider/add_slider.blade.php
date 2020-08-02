@extends('layouts.layout')

@section("content")
<addslidercomponent    :slider_data="{{ json_encode($slider_data) }}"></addslidercomponent>
@endsection