@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">Categories</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_CATEGORY'), true))
                    <a href="{{ route('add_category')}}" role="button" class="btn btn-primary">New Category</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Category Code</th>
                        <th>Status</th>
                        <th>Discount Code</th>
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
    <script src="{{ asset('js/pages/categories.js') }}"></script>
    <script>
        'use strict';
        var categories = new Categories();
        categories.load_listing_table();
    </script>
@endpush