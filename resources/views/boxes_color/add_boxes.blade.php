@extends('layouts.layout')

@section("content")
<addboxescolorcomponent   :statuses="{{ json_encode($statuses) }}"  :boxes="{{ json_encode($boxes) }}"  :boxes_data="{{ json_encode($boxes_data) }}"></addboxescolorcomponent>
@endsection