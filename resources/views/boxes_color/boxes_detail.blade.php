@extends('layouts.layout')

@section("content")
<boxescolordetailcomponent :boxes_data="{{ json_encode($boxes_data) }}"></boxescolordetailcomponent>
@endsection