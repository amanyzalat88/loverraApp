@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">Ads   </span>
            </div>
           <!-- <div class="">
                @if (check_access(array('A_ADD_ADS'), true))
                    <a href="{{ route('add_ads')}}" role="button" class="btn btn-primary">New Ads</a>
                @endif
            </div>-->
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>title Ar</th>
                        <th>title En</th>
                        <th>Category name</th>
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
    <script src="{{ asset('js/pages/ads.js') }}"></script>
    <script>
        'use strict';
        var ads = new Ads();
        ads.load_listing_table();
    </script>
@endpush