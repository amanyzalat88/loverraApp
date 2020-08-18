@extends('layouts.layout')

@section("content")
<addcategorycomponent :statuses="{{ json_encode($statuses) }}"  :discount_codes="{{ json_encode($discount_codes) }}" :categories="{{ json_encode($categories) }}" :category_data="{{ json_encode($category_data) }}"></addcategorycomponent>
@endsection