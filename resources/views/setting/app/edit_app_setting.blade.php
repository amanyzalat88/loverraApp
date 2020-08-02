@extends('layouts.layout')

@section("content")
<editappsettingcomponent :date_time_formats ="{{ json_encode($date_time_formats) }}" :date_formats ="{{ json_encode($date_formats) }}" :setting_data="{{ json_encode($setting_data) }}"></editappsettingcomponent>
@endsection