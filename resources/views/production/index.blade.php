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
                        <div class="card-body table-responsive">
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label for="fp-range" class="font-weight-bold">DATE RANGE</label>
                                    <input type="text" id="fp-range" class="form-control flatpickr-range"
                                           placeholder="YYYY-MM-DD to YYYY-MM-DD" name="date_range"/>
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

            if (sessionStorage.getItem('production_date_range')) {
                $('input[name="date_range"]').val(sessionStorage.getItem('production_date_range'));
            }


            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('productions.index') }}",
                    data: function (d) {
                        d.date_range = $('input[name="date_range"]').val();
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
        })

        $('#fp-range').on('change', function () {
            sessionStorage.setItem('production_date_range', $('input[name="date_range"]').val());
            recallDatatable();
        })

        function recallDatatable() {
            $('#dataTable').DataTable().draw(true);
        }
    </script>
@endpush
