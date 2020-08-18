@extends('layouts.layout')

@section("content")
<addcontactcomponent     :contact_data="{{ json_encode($contact_data) }}"></addcontactcomponent>
@endsection