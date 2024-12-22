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
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="">From Date</label>
                                <input type="date" name="from_date" id="from_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="">To Date</label>
                                <input type="date" name="to_date" id="to_date" class="form-control">
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

        function recallDatatable() {
                $('#dataTable').DataTable().draw(true);
            }

        $(document).ready(function () {
            
            if (sessionStorage.getItem('status')) {
                $('select[name="status"]').val(sessionStorage.getItem('status'));
            }
            if (sessionStorage.getItem('from_date')) {
                $('input[name="from_date"]').val(sessionStorage.getItem('from_date'));
            }
            if (sessionStorage.getItem('to_date')) {
                $('input[name="to_date"]').val(sessionStorage.getItem('to_date'));
            }

            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('sales-deliveries.index') }}",
                    data: function (d) {
                        d.status = $('select[name="status"]').val();
                        d.from_date = $('input[name="from_date"]').val();
                        d.to_date = $('input[name="to_date"]').val();
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

            $('#from_date').on('change', function () {
            sessionStorage.setItem('from_date', $('input[name="from_date"]').val());
                recallDatatable();
            })
            $('#to_date').on('change', function () {
            sessionStorage.setItem('to_date', $('input[name="to_date"]').val());
                recallDatatable();
            })
            $('#status').on('change', function () {
                sessionStorage.setItem('status', $('select[name="status"]').val());
                recallDatatable();
            });
            })
            $('select[name="status"], input[name="from_date"], input[name="to_date"]').on('change', function () {
            sessionStorage.setItem('status', $('select[name="status"]').val());
            sessionStorage.setItem('from_date', $('input[name="from_date"]').val());
            sessionStorage.setItem('to_date', $('input[name="to_date"]').val());
            recallDatatable();
        });

        $('#search-btn').on('click', function () {
            recallDatatable();
        });
    </script>
@endpush
