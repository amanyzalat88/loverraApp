@extends('layouts.layout')

@section("content")
<adsdetailcomponent :ads_data="{{ json_encode($ads_data) }}"></adsdetailcomponent>
@endsection