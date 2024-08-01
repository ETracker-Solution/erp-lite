@extends('layouts.app')
@section('title')
Earn Point List
@endsection
@section('content')

@php
$links = [
'Home'=>route('dashboard'),
'Loyalty Module'=>'',
'Loyalty Entry'=>'',
'Earn Point list'=>''
]
@endphp
<x-breadcrumb title='Earn Point list' :links="$links"/>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">All Earn Point List</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <table id="dataTable" class="table table-bordered table-hover">
                            {{-- show from datatable--}}
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

            </div>
        </div>
        <!-- /.row -->

    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection
@section('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
@endsection
@push('style')

@endpush
@section('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('earn-points.index') }}",
                },
                columns: [{
                    data: "DT_RowIndex",
                    title: "SL",
                    name: "DT_RowIndex",
                    searchable: false,
                    orderable: false
                },
                    {
                        data: "customer.name",
                        title: "customer",
                        searchable: true,
                        "defaultContent": "No Set"
                    },
                    {
                        data: "sale.invoice_number",
                        title: "Invoice Number",
                        searchable: true,
                        "defaultContent": "No Set"
                    }, {
                        data: "point",
                        title: "point",
                        searchable: true
                    },
                    {
                        data: "created_at",
                        title: "created at",
                        searchable: true
                    },
                    // {
                    //     data: "action",
                    //     title: "Action",
                    //     orderable: false,
                    //     searchable: false
                    // },
                ],
            });
        })
    </script>

@endpush
