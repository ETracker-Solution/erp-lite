@extends('layouts.app')

@section('title', 'Redeem Point List')
@push('style')
<link rel="stylesheet" type="text/css"
    href="{{asset('datepicker/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css"
    href="{{asset('datepicker/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
@endpush
@section('content')
@php
$links = [
'Home'=>route('dashboard'),
'Loyalty Module'=>'',
'Loyalty Entry'=>'',
'Redeem Point list'=>''
]
@endphp
<x-breadcrumb title='Redeem Point list' :links="$links" />
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
                            <label for="customer_name">Customer</label>
                            <input type="text" id="customer_name" class="form-control filter-input" placeholder="Customer Name">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="invoice_no">Invoice Number</label>
                            <input type="text" id="invoice_no" class="form-control filter-input" placeholder="Invoice Number">
                        </div>
                    </div>
                    <div class="col-md-3">
                         <div class="form-group mb-0">
                            <button type="button" id="reset-btn" class="btn btn-secondary btn-block">Reset</button>
                        </div>
                    </div>
                </div>
                <hr>

                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">All Redeem Point List</h3>
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
<script src="{{ asset('datepicker/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
@endsection
@push('script')
<script>
    function recallDatatable() {
        $('#dataTable').DataTable().draw(true);
    }

    $(document).ready(function () {
            if (sessionStorage.getItem('date_range_redeem')) {
                $('#date_range').val(sessionStorage.getItem('date_range_redeem'));
            }
            if (sessionStorage.getItem('customer_name_redeem')) {
                $('#customer_name').val(sessionStorage.getItem('customer_name_redeem'));
            }
            if (sessionStorage.getItem('invoice_no_redeem')) {
                $('#invoice_no').val(sessionStorage.getItem('invoice_no_redeem'));
            }

            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('redeem-points.index') }}",
                    data: function (d) {
                        d.date_range = $('#date_range').val();
                        d.customer_name = $('#customer_name').val();
                        d.invoice_no = $('#invoice_no').val();
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
                        data: "customer.name",
                        title: "customer",
                        name: "customer.name",
                        searchable: true,
                        "defaultContent": "No Set"
                    },
                    {
                        data: "sale.invoice_number",
                        title: "Invoice Number",
                        name: "sale.invoice_number",
                        searchable: true,
                        "defaultContent": "No Set"
                    }, {
                        data: "point",
                        title: "point",
                        name: "point",
                        searchable: true
                    },
                    {
                        data: "created_at",
                        title: "created at",
                        name: "created_at",
                        searchable: true
                    },
                ],
            });

            $('.flatpickr-range').flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
            });

            $('.filter-input').on('keyup change', function () {
                sessionStorage.setItem('date_range_redeem', $('#date_range').val());
                sessionStorage.setItem('customer_name_redeem', $('#customer_name').val());
                sessionStorage.setItem('invoice_no_redeem', $('#invoice_no').val());
                recallDatatable();
            });

            $('#reset-btn').on('click', function () {
                $('.filter-input').val('');
                if ($('#date_range').length > 0 && $('#date_range')[0]._flatpickr) {
                    $('#date_range')[0]._flatpickr.clear();
                }
                sessionStorage.removeItem('date_range_redeem');
                sessionStorage.removeItem('customer_name_redeem');
                sessionStorage.removeItem('invoice_no_redeem');
                recallDatatable();
            });
        })
</script>

@endpush
