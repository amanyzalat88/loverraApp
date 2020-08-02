@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">Purchase Orders</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_PURCHASE_ORDER'), true))
                    <a href="{{ route('add_purchase_order')}}" role="button" class="btn btn-primary">New Purchase Order</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>PO Number</th>
                        <th>PO Reference #</th>
                        <th>Supplier Name</th>
                        <th>Order Date</th>
                        <th>Order Due Date</th>
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
    <script src="{{ asset('js/pages/purchase_orders.js') }}"></script>
    <script>
        'use strict';
        var purchase_orders = new PurchaseOrders();
        purchase_orders.load_listing_table();
    </script>
@endpush