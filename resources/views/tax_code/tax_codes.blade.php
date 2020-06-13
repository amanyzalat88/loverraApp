@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">Tax Codes</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_TAXCODE'), true))
                    <a href="{{ route('add_tax_code')}}" role="button" class="btn btn-primary">New Tax Code</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Tax Code</th>
                        <th>Tax Percentage</th>
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
    <script src="{{ asset('js/pages/taxcodes.js') }}"></script>
    <script>
        'use strict';
        var taxcodes = new Taxcodes();
        taxcodes.load_listing_table();
    </script>
@endpush