@extends('layouts.layout')

@section("content")
<orderdetailcomponent :order_data="{{ json_encode($order_data) }}" :delete_order_access="{{ json_encode($delete_order_access) }}"></orderdetailcomponents>
@endsection