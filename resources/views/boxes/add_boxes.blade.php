@extends('layouts.layout')

@section("content")
<addboxescomponent   :statuses="{{ json_encode($statuses) }}"  :boxes_data="{{ json_encode($boxes_data) }}"></addboxescomponent>
@endsection