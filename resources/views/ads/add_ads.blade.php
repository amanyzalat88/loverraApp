@extends('layouts.layout')

@section("content")
<addadscomponent   :statuses="{{ json_encode($statuses) }}"  :categories="{{ json_encode($categories) }}" :ads_data="{{ json_encode($ads_data) }}"></addadscomponent>
@endsection