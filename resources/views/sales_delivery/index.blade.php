@extends('layouts.app')
@section('title')
    Sales Delivery
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Sales Delivery'=>''
        ]
    @endphp
    <x-breadcrumb title='Sales Delivery' :links="$links"/>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="row align-items-end mb-3">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control filter-input">
                                    <option value="">All</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="date_range">Date Range</label>
                                <input type="text" id="date_range" class="form-control filter-input flatpickr-range" placeholder="YYYY-MM-DD to YYYY-MM-DD">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="invoice_no">Invoice No</label>
                                <input type="text" id="invoice_no" class="form-control filter-input" placeholder="Invoice No">
                            </div>
                        </div>
                        <div class="col-md-2">
                             <div class="form-group mb-0">
                                <button type="button" id="reset-btn" class="btn btn-secondary btn-block">Reset</button>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Sales Delivery List</h3>
                            <div class="card-tools">
                                <a href="{{route('sales-deliveries.create')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                        aria-hidden="true"></i> &nbsp;Make Delivery
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="dataTable" class="table table-bordered">
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

        $(document).ready(function () {
            
            if (sessionStorage.getItem('status')) {
                $('#status').val(sessionStorage.getItem('status'));
            }
            if (sessionStorage.getItem('date_range')) {
                $('#date_range').val(sessionStorage.getItem('date_range'));
            }
            if (sessionStorage.getItem('invoice_no')) {
                $('#invoice_no').val(sessionStorage.getItem('invoice_no'));
            }

            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('sales-deliveries.index') }}",
                    data: function (d) {
                        d.status = $('#status').val();
                        d.date_range = $('#date_range').val();
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
                        data: "invoice_number",
                        title: "Invoice No",
                        searchable: true
                    },
                    {
                        data: "outlet.name",
                        title: "Order From",
                        searchable: true,
                        "defaultContent": "N/A"
                    },
                    {
                        data: "delivery_point.name",
                        name: "deliveryPoint.name",
                        title: "Delivery Point",
                        searchable: true
                    },

                    {
                        data: "grand_total",
                        title: "Grand Total",
                        searchable: true
                    },
                    {
                        data: "due",
                        title: "Due",
                        searchable: true
                    },
                    {
                        data: "status",
                        title: "Delivery Status",
                        searchable: false
                    },
                    {
                        data: "created_at",
                        title: "Date",
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

            $('.filter-input').on('change keyup', function () {
                sessionStorage.setItem('status', $('#status').val());
                sessionStorage.setItem('date_range', $('#date_range').val());
                sessionStorage.setItem('invoice_no', $('#invoice_no').val());
                recallDatatable();
            });

            $('#reset-btn').on('click', function () {
                $('.filter-input').val('');
                if ($('#date_range').length > 0 && $('#date_range')[0]._flatpickr) {
                    $('#date_range')[0]._flatpickr.clear();
                }
                sessionStorage.removeItem('status');
                sessionStorage.removeItem('date_range');
                sessionStorage.removeItem('invoice_no');
                recallDatatable();
            });
        });
    </script>
@endpush

