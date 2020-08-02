@extends('layouts.layout')

@section("content")
<addpurchaseordercomponent :currency_list="{{ json_encode($currency_list) }}" :purchase_order_data="{{ json_encode($purchase_order_data) }}"></addpurchaseordercomponent>
@endsection