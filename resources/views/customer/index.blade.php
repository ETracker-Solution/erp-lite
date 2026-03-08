@extends('layouts.app')
@section('title')
Customer List
@endsection
@section('content')
<!-- Content Header (Page header) -->

@php
    $links = [
    'Home'=>route('dashboard'),
    'Master Data'=>'',
    'Customer list'=>''
    ]
@endphp
<x-breadcrumb title='Customer' :links="$links"/>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="row align-items-end mb-3">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="date_range">Date Range</label>
                            <input type="text" id="date_range" class="form-control filter-input flatpickr-range" placeholder="YYYY-MM-DD to YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="name">Name</label>
                            <input type="text" id="name" class="form-control filter-input" placeholder="Customer Name">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="email">Email</label>
                            <input type="text" id="email" class="form-control filter-input" placeholder="Email">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label for="mobile">Mobile</label>
                            <input type="text" id="mobile" class="form-control filter-input" placeholder="Mobile No">
                        </div>
                    </div>
                    <div class="col-md-1">
                         <div class="form-group mb-0">
                            <button type="button" id="reset-btn" class="btn btn-secondary btn-block">Reset</button>
                        </div>
                    </div>
                </div>
                <hr>

                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Customer List</h3>
                        <div class="card-tools">
                            <a href="{{route('customers.create')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp;Add New</button></a>
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
    <link rel="stylesheet" type="text/css" href="{{ asset('datepicker/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('datepicker/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
@endsection
@section('js')
<!-- DataTables -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{ asset('datepicker/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
@endsection
@push('script')
<!-- page script -->
<script>
    function recallDatatable() {
        $('#dataTable').DataTable().draw(true);
    }

    $(document).ready(function() {
        if (sessionStorage.getItem('date_range_customer')) {
            $('#date_range').val(sessionStorage.getItem('date_range_customer'));
        }
        if (sessionStorage.getItem('name_customer')) {
            $('#name').val(sessionStorage.getItem('name_customer'));
        }
        if (sessionStorage.getItem('email_customer')) {
            $('#email').val(sessionStorage.getItem('email_customer'));
        }
        if (sessionStorage.getItem('mobile_customer')) {
            $('#mobile').val(sessionStorage.getItem('mobile_customer'));
        }

        $('#dataTable').dataTable({
            stateSave: true,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ route('customers.index') }}",
                data: function (d) {
                    d.date_range = $('#date_range').val();
                    d.name = $('#name').val();
                    d.email = $('#email').val();
                    d.mobile = $('#mobile').val();
                }
            },
            columns: [{
                data: "DT_RowIndex",
                title: "SL",
                name: "DT_RowIndex",
                searchable: false,
                orderable: false
            },
                {
                    data: "name",
                    title: "Name",
                    name: "name",
                    searchable: true
                },
                {
                    data: "mobile",
                    title: "Mobile",
                    name: "mobile",
                    searchable: true
                },
                {
                    data: "address",
                    title: "Address",
                    name: "address",
                    searchable: true
                },
                {
                    data: "email",
                    title: "email",
                    name: "email",
                    searchable: true
                },
                {
                    data: "created_at",
                    title: "created at",
                    name: "created_at",
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

        $('.flatpickr-range').flatpickr({
            mode: "range",
            dateFormat: "Y-m-d",
        });

        $('.filter-input').on('keyup change', function () {
            sessionStorage.setItem('date_range_customer', $('#date_range').val());
            sessionStorage.setItem('name_customer', $('#name').val());
            sessionStorage.setItem('email_customer', $('#email').val());
            sessionStorage.setItem('mobile_customer', $('#mobile').val());
            recallDatatable();
        });

        $('#reset-btn').on('click', function () {
            $('.filter-input').val('');
            if ($('#date_range').length > 0 && $('#date_range')[0]._flatpickr) {
                $('#date_range')[0]._flatpickr.clear();
            }
            sessionStorage.removeItem('date_range_customer');
            sessionStorage.removeItem('name_customer');
            sessionStorage.removeItem('email_customer');
            sessionStorage.removeItem('mobile_customer');
            recallDatatable();
        });
    })
</script>

@endpush
