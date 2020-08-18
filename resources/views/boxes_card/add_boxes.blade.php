@extends('layouts.layout')

@section("content")
<addboxescardcomponent   :statuses="{{ json_encode($statuses) }}"  :boxes_data="{{ json_encode($boxes_data) }}"></addboxescardcomponent>
@endsection