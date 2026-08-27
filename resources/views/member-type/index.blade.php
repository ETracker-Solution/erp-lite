@extends('layouts.app')

@section('title', 'Member Type List')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Member Type' => '',
        ];
    @endphp
    <x-breadcrumb title="Member Type List" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Member Type List</h3>
                            <div class="card-tools">
                                <a href="{{ route('member-types.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="dataTable" class="table table-bordered table-hover table-sm"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
@endsection
@section('js')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $('#dataTable').dataTable({
                stateSave: false,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('member-types.index') }}",
                },
                columns: [
                    {data: "DT_RowIndex", title: "SL", name: "DT_RowIndex", searchable: false, orderable: false},
                    {data: "name", title: "Name", searchable: true},
                    {data: "from_point", title: "From Point", searchable: false, className: "text-right"},
                    {data: "to_point", title: "To Point", searchable: false, className: "text-right"},
                    {data: "minimum_purchase", title: "Min Purchase", searchable: false, className: "text-right"},
                    {data: "discount", title: "Discount", searchable: false, className: "text-right"},
                    {data: "created_at", title: "Created At", searchable: false, orderable: false},
                    {data: "action", title: "Action", orderable: false, searchable: false},
                ],
            });
        });
    </script>
@endpush
