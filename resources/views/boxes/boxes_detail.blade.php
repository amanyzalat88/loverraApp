@extends('layouts.layout')

@section("content")
<boxesdetailcomponent :boxes_data="{{ json_encode($boxes_data) }}"></boxesdetailcomponent>
@endsection