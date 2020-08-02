@extends('layouts.order_layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/billing.css') }}">
@endpush

@section("content")
<addordercomponent :store_currency = "{{ json_encode($store_currency) }}" :store_tax_percentage="{{ json_encode($store_tax_percentage) }}" :store_discount_percentage="{{ json_encode($store_discount_percentage) }}" :payment_methods="{{ json_encode($payment_methods) }}" :categories="{{ json_encode($categories) }}" :order_data="{{ json_encode($order_data) }}"></addordercomponent>
@endsection