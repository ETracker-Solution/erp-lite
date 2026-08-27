@extends('layouts.app')
@section('title', 'FG Requisition Delivery List')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'FG Requisition Delivery list' => '',
        ];
    @endphp
    <x-breadcrumb title="FG Requisition Delivery" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">FG Requisition Delivery List</h3>
                            <div class="card-tools">
                                <a href="{{ route('fg-requisition-deliveries.create') }}" class="btn btn-sm btn-primary">
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
                    url: "{{ route('fg-requisition-deliveries.index') }}",
                },
                columns: [
                    {data: "DT_RowIndex", title: "SL", name: "DT_RowIndex", searchable: false, orderable: false},
                    {data: "uid", title: "FGRD No", searchable: true, defaultContent: "—"},
                    {data: "requisition.uid", name: "requisition.uid", title: "FGR No", searchable: true, defaultContent: "—"},
                    {data: "date", title: "Date", searchable: true, defaultContent: "—"},
                    {data: "from_store.name", name: "fromStore.name", title: "From Store", searchable: true, defaultContent: "—"},
                    {data: "to_store.name", name: "toStore.name", title: "To Store", searchable: true, defaultContent: "—"},
                    {data: "total_quantity", title: "Qty", searchable: false, className: "text-right", defaultContent: "0.00"},
                    {data: "status", title: "Status", searchable: false, orderable: false},
                    {data: "created_at", title: "Created At", searchable: false, orderable: false},
                    {data: "action", title: "Action", orderable: false, searchable: false},
                ],
            });
        });
    </script>
@endpush
