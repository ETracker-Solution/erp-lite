@extends('layouts.app')
@section('title')
    Purchase Return List
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Purchase Return list'=>''
        ]
    @endphp
    <x-breadcrumb title='Goods Purchase Return' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title">Goods Purchase Return List</h3>
                            <div class="card-tools">
                                <a href="{{route('purchase-returns.create')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                              aria-hidden="true"></i> &nbsp;Add New
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="date_range">Date Range</label>
                                    <input type="text" id="date_range" class="form-control filter-input flatpickr-range" placeholder="YYYY-MM-DD to YYYY-MM-DD">
                                </div>
                                <div class="col-md-2">
                                    <label for="purchase_return_id">Purchase Return No</label>
                                    <input type="text" id="purchase_return_id" class="form-control filter-input" placeholder="Return No">
                                </div>
                                <div class="col-md-2">
                                    <label for="supplier">Supplier</label>
                                    <input type="text" id="supplier" class="form-control filter-input" placeholder="Supplier Name">
                                </div>
                                <div class="col-md-2">
                                    <label for="store">Store</label>
                                    <input type="text" id="store" class="form-control filter-input" placeholder="Store Name">
                                </div>
                                <div class="col-md-2">
                                    <label for="status">Status</label>
                                    <select id="status" class="form-control filter-input">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="received">Received</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <button id="reset_filter" class="btn btn-warning btn-block">Reset</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="dataTable" class="table table-bordered">
                                    {{-- show from datatable--}}
                                </table>
                            </div>
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
    <!-- page script -->
    <script>
        $(document).ready(function () {
        var table = $('#dataTable').DataTable({
            stateSave: true,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ route('purchase-returns.index') }}",
                data: function (d) {
                    d.date_range = $('#date_range').val();
                    d.id = $('#purchase_return_id').val();
                    d.supplier = $('#supplier').val();
                    d.store = $('#store').val();
                    d.status = $('#status').val();
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
                        data: "id",
                        title: "Purchase Return No",
                        searchable: true
                    },
                    {
                        data: "supplier.name",
                        title: "Supplier",
                        searchable: true
                    }, {
                        data: "store.name",
                        title: "Store",
                        searchable: true
                    },
                    {
                        data: "subtotal",
                        title: "Subtotal",
                        searchable: true
                    },
                    {
                        data: "vat",
                        title: "Vat Amount",
                        searchable: true
                    },
                    {
                        data: "net_payable",
                        title: "Net Payable",
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
        })
    </script>
@endpush
