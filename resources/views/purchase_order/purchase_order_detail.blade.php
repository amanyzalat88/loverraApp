@extends('layouts.layout')

@section("content")
<purchaseorderdetailcomponent :po_statuses="{{ json_encode($po_statuses) }}" :purchase_order_data="{{ json_encode($purchase_order_data) }}"></purchaseorderdetailcomponent>
@endsection