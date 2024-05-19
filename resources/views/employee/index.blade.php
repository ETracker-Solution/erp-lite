@extends('layouts.app')
@section('title')
Employee List
@endsection
@section('content')
<!-- Content Header (Page header) -->

@php
    $links = [
    'Home'=>route('dashboard'),
    'Employee list'=>''
    ]
@endphp
<x-breadcrumb title='Employee' :links="$links"/>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Employee List</h3>
                        <div class="card-tools">
                            <a href="{{route('employees.create')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp;Add Employee</button></a>
                        </div>
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
@section('js')
<!-- DataTables -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection
@push('script')
<!-- page script -->
<script>
    $(document).ready(function() {
        $('#dataTable').dataTable({
            stateSave: true,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ route('employees.index') }}",
            },
            columns: [{
                data: "DT_RowIndex",
                title: "SL",
                name: "DT_RowIndex",
                searchable: false,
                orderable: false
            },
                {
                    data: "employee_id",
                    title: "Employee ID",
                    searchable: true
                },
                {
                    data: "name",
                    title: "Name",
                    searchable: true
                },
                {
                    data: "email",
                    title: "email",
                    searchable: true
                },
                {
                    data: "phone",
                    title: "Phone",
                    searchable: true
                },
                {
                    data: "present_address",
                    title: "Present Address",
                    searchable: true
                },
                {
                    data: "created_at",
                    title: "created at",
                    searchable: true
                },
                {
                    data: "action",
                    title: "Action",
                    orderable: false,
                    searchable: false
                },
            ],
        });
    })
</script>

@endpush
