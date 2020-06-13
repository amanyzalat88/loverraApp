@extends('layouts.layout')

@section("content")
<addstorecomponent :statuses="{{ json_encode($statuses) }}" :tax_codes="{{ json_encode($tax_codes) }}" :discount_codes="{{ json_encode($discount_codes) }}" :store_data="{{ json_encode($store_data) }}" :invoice_print_types="{{ json_encode($invoice_print_types) }}" :currency_list="{{ json_encode($currency_list) }}"></addstorecomponent>
@endsection