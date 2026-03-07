@extends('layouts.app')
@section('title','FG Production List')

@section('content')
    <section class="content-header">
        @php
            $links = [
            'Home'=>route('dashboard'),
            'FG Production list'=>''
            ]
        @endphp
        <x-bread-crumb-component title='FG Production' :links="$links"/>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title">FG Production List</h3>
                            <div class="card-tools">
                                <a href="{{route('productions.create')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                              aria-hidden="true"></i> &nbsp;Add New
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="date_range">Date Range</label>
                                    <input type="text" id="date_range" class="form-control filter-input flatpickr-range" placeholder="YYYY-MM-DD to YYYY-MM-DD">
                                </div>
                                <div class="col-md-2">
                                    <label for="batch_no">Batch</label>
                                    <input type="text" id="batch_no" class="form-control filter-input" placeholder="Batch No">
                                </div>
                                <div class="col-md-2">
                                    <label for="factory">Production Unit</label>
                                    <input type="text" id="factory" class="form-control filter-input" placeholder="Factory Name">
                                </div>
                                <div class="col-md-2">
                                    <label for="store">FG Store</label>
                                    <input type="text" id="store" class="form-control filter-input" placeholder="Store Name">
                                </div>
                                <div class="col-md-1">
                                    <button id="reset_filter" class="btn btn-warning btn-block">Reset</button>
                                </div>
                                <div class="col-md-1">
                                    <form method="GET" action="{{route('fg.production.export','xlsx')}}" id="excelForm">
                                        @csrf
                                        <button class="btn btn-success" type="button" id="excel-btn">EXCEL</button>
                                    </form>
                                </div>
                            </div>

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
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/css/plugins/forms/pickers/form-pickadate.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
@endsection
@push('style')
@endpush
@section('js')
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/pickadate/picker.time.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/pickadate/legacy.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection
@push('script')
    <!-- page script -->
    <script>
        $(document).ready(function () {

            var table = $('#dataTable').DataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('productions.index') }}",
                    data: function (d) {
                        d.date_range = $('#date_range').val();
                        d.batch_no = $('#batch_no').val();
                        d.factory = $('#factory').val();
                        d.store = $('#store').val();
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
                        data: "batch.batch_no",
                        title: "Batch",
                        searchable: true,
                        "defaultContent": "Not Set"
                    }, {
                        data: "factory.name",
                        title: "Production Unit(Factory)",
                        searchable: true,
                        "defaultContent": "Not Set"
                    }, {
                        data: "store.name",
                        title: "FG Store",
                        searchable: true,
                        "defaultContent": "Not Set"
                    },

                    {
                        data: "total_quantity",
                        title: "Total Quantity",
                        searchable: true
                    },

                    {
                        data: "subtotal",
                        title: "Sub Total",
                        searchable: true
                    },
                    {
                        data: "status",
                        title: "Status",
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
                table.draw();
            });

            $('#reset_filter').click(function () {
                $('.filter-input').val('');
                if ($('#date_range').length > 0) {
                    $('#date_range')[0]._flatpickr.clear();
                }
                table.draw();
            });

            $(document).on("click", "#excel-btn", function (e) {
                e.preventDefault();
                let form = $("#excelForm");
                let date_range = $('#date_range').clone().attr('name', 'date_range');
                form.find('input[name="date_range"]').remove();
                // form.append(date_range);
                form.submit();
            });
        })
    </script>
@endpush
