@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">Products</span>
            </div>
            <div class="">
                @if (check_access(array('A_GENERATE_BARCODE_PRODUCT'), true))
                    <a href="{{ route('generate_barcode')}}" role="button" class="btn btn-outline-primary">Generate Barcode</a>
                @endif
                @if (check_access(array('A_ADD_PRODUCT'), true))
                    <a href="{{ route('add_product')}}" role="button" class="btn btn-primary">New Product</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>Prodcut Code</th>
                        <th>Name</th>
                        <th>Supplier</th>
                        <th>Category</th>
                       
                        <th>Discount Code</th>
                        <th>Quantity</th>
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
    <script src="{{ asset('js/pages/products.js') }}"></script>
    <script>
        'use strict';
        var products = new Products();
        products.load_listing_table();
    </script>
@endpush