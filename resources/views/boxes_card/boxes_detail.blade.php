@extends('layouts.layout')

@section("content")
<boxescarddetailcomponent :boxes_data="{{ json_encode($boxes_data) }}"></boxescarddetailcomponent>
@endsection