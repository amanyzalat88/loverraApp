@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">Orders</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_ORDER'), true))
                    <a href="{{ route('add_order')}}" role="button" class="btn btn-primary">New Order</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Customer Phone</th>
                        <th>Customer Email</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Created On</th>
                        <th>Updated On</th>
                        <th>Created By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/datatable.js') }}"></script>
    <script src="{{ asset('js/pages/orders.js') }}"></script>
    <script>
        'use strict';
        var orders = new Orders();
        orders.load_listing_table();
    </script>
@endpush