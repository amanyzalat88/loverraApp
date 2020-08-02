@extends('layouts.layout')

@section("content")
<addproductcomponent :statuses="{{ json_encode($statuses) }}" :suppliers="{{ json_encode($suppliers) }}" :categories="{{ json_encode($categories) }}" :taxcodes="{{ json_encode($taxcodes) }}"  :suppliers="{{ json_encode($suppliers) }}" :categories="{{ json_encode($categories) }}" :discount_codes="{{ json_encode($discount_codes) }}" :product_data="{{ json_encode($product_data) }}"></addproductcomponent>
@endsection