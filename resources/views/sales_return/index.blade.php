@extends('layouts.app')
@section('title')
    Sales Return
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Sales Return'=>''
        ]
    @endphp
    <x-breadcrumb title='Sales Return' :links="$links"/>

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
                                <label for="uid">Return No</label>
                                <input type="text" id="uid" class="form-control filter-input" placeholder="Return No">
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
                            <h3 class="card-title">Sales Return List</h3>
                            <div class="card-tools">
                                <a href="{{route('sales-returns.create')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                              aria-hidden="true"></i> &nbsp;Add New
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
            if (sessionStorage.getItem('date_range')) {
                $('#date_range').val(sessionStorage.getItem('date_range'));
            }
            if (sessionStorage.getItem('uid')) {
                $('#uid').val(sessionStorage.getItem('uid'));
            }

            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('sales-returns.index') }}",
                    data: function (d) {
                        d.date_range = $('#date_range').val();
                        d.uid = $('#uid').val();
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
                        data: "uid",
                        title: "Return No",
                        searchable: true
                    },
                    {
                        data: "sale_invoice",
                        title: "Sale Invoice",
                        searchable: true
                    },
                    {
                        data: "subtotal",
                        title: "Subtotal",
                        searchable: false
                    },
                    {
                        data: "discount",
                        title: "Discount",
                        searchable: false
                    }, {
                        data: "grand_total",
                        title: "Grand Total",
                        searchable: false
                    },
                    {
                        data: "status",
                        title: "Status",
                        searchable: false
                    },
                    {
                        data: "return_date",
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
                sessionStorage.setItem('date_range', $('#date_range').val());
                sessionStorage.setItem('uid', $('#uid').val());
                recallDatatable();
            });

            $('#reset-btn').on('click', function () {
                $('.filter-input').val('');
                if ($('#date_range').length > 0 && $('#date_range')[0]._flatpickr) {
                    $('#date_range')[0]._flatpickr.clear();
                }
                sessionStorage.removeItem('date_range');
                sessionStorage.removeItem('uid');
                recallDatatable();
            });
        })
    </script>
@endpush

