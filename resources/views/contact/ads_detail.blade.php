@extends('layouts.layout')

@section("content")
<contactdetailcomponent :contact_data="{{ json_encode($contact_data) }}"></contactdetailcomponent>
@endsection