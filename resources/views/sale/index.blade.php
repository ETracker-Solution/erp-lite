@extends('layouts.app')
@section('title')
    Sales List
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Sales list'=>''
        ]
    @endphp
    <x-breadcrumb title='Sales' :links="$links"/>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Sales List</h3>
                            <div class="card-tools">
                                <a href="{{route('sales.create')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                              aria-hidden="true"></i> &nbsp;Add Sale
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 align-items-end">
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
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label for="outlet_id">Outlet</label>
                                        <select id="outlet_id" class="form-control select2 filter-input">
                                            <option value="">Select Outlet</option>
                                            @foreach($outlets as $outlet)
                                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label for="delivery_point_id">Delivery Point</label>
                                        <select id="delivery_point_id" class="form-control select2 filter-input">
                                            <option value="">Select Delivery Point</option>
                                            @foreach($outlets as $outlet)
                                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label for="payment_method">Payment Method</label>
                                        <select id="payment_method" class="form-control select2 filter-input">
                                            <option value="">Select Payment Method</option>
                                            <option value="cash">Cash</option>
                                            <option value="bkash">Bkash</option>
                                            <option value="nagad">Nagad</option>
                                            <option value="rocket">Rocket</option>
                                            <option value="nexus">Nexus</option>
                                            <option value="city">City</option>
                                            <option value="pbl">PBL</option>
                                            <option value="upay">Upay</option>
                                            <option value="DBBL">DBBL</option>
                                            <option value="UCB">UCB</option>
                                            <option value="prime">Prime</option>
                                            <option value="foodie">Foodie</option>
                                            <option value="foodpanda">FoodPanda</option>
                                            <option value="point">Point</option>
                                            <option value="due">Due</option>
                                            <option value="advance">Advance</option>
                                            <option value="exchange">Exchange</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label for="product_id">Item</label>
                                        <select id="product_id" class="form-control select2 filter-input">
                                            <option value="">Select Item</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <label for="group_id">Group</label>
                                        <select id="group_id" class="form-control select2 filter-input">
                                            <option value="">Select Group</option>
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <button class="btn btn-secondary btn-block" id="reset-btn">Reset</button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mb-0">
                                        <button class="btn btn-success btn-block" id="export-btn">Export to Excel</button>
                                    </div>
                                </div>
                            </div>
                            <hr>
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
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .select2-container--bootstrap4 .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
        }
    </style>
@endsection
@section('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
@endsection
@push('script')
    <!-- page script -->
    <script>
        function recallDatatable() {
            $('#dataTable').DataTable().draw(true);
        }

        $(document).ready(function () {
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            if (sessionStorage.getItem('date_range_sale')) {
                $('#date_range').val(sessionStorage.getItem('date_range_sale'));
            }
            if (sessionStorage.getItem('invoice_no_sale')) {
                $('#invoice_no').val(sessionStorage.getItem('invoice_no_sale'));
            }
            if (sessionStorage.getItem('outlet_id_sale')) {
                $('#outlet_id').val(sessionStorage.getItem('outlet_id_sale')).trigger('change');
            }
            if (sessionStorage.getItem('delivery_point_id_sale')) {
                $('#delivery_point_id').val(sessionStorage.getItem('delivery_point_id_sale')).trigger('change');
            }
            if (sessionStorage.getItem('payment_method_sale')) {
                $('#payment_method').val(sessionStorage.getItem('payment_method_sale')).trigger('change');
            }
            if (sessionStorage.getItem('product_id_sale')) {
                $('#product_id').val(sessionStorage.getItem('product_id_sale')).trigger('change');
            }
            if (sessionStorage.getItem('group_id_sale')) {
                $('#group_id').val(sessionStorage.getItem('group_id_sale')).trigger('change');
            }

            let table = $('#dataTable').DataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('sales.index') }}",
                    data: function (d) {
                        d.date_range = $('#date_range').val();
                        d.invoice_no = $('#invoice_no').val();
                        d.outlet_id = $('#outlet_id').val();
                        d.delivery_point_id = $('#delivery_point_id').val();
                        d.payment_method = $('#payment_method').val();
                        d.product_id = $('#product_id').val();
                        d.group_id = $('#group_id').val();
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
                        name: "invoice_number",
                        searchable: true
                    },
                    {
                        data: "subtotal",
                        title: "Subtotal",
                        name: "subtotal",
                        searchable: true
                    },
                    {
                        data: "discount",
                        title: "Discount",
                        name: "discount",
                        searchable: true
                    },
                    {
                        data: "grand_total",
                        title: "Grand Total",
                        name: "grand_total",
                        searchable: true
                    },
                    {
                        data: "status",
                        title: "Status",
                        name: "status",
                        searchable: false
                    },
                    {
                        data: "created_at",
                        title: "Date",
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

            $('.filter-input').on('change keyup', function () {
                sessionStorage.setItem('date_range_sale', $('#date_range').val());
                sessionStorage.setItem('invoice_no_sale', $('#invoice_no').val());
                sessionStorage.setItem('outlet_id_sale', $('#outlet_id').val());
                sessionStorage.setItem('delivery_point_id_sale', $('#delivery_point_id').val());
                sessionStorage.setItem('payment_method_sale', $('#payment_method').val());
                sessionStorage.setItem('product_id_sale', $('#product_id').val());
                sessionStorage.setItem('group_id_sale', $('#group_id').val());
                recallDatatable();
            });

            $('#export-btn').on('click', function () {
                let url = "{{ route('sales.export') }}";
                let params = {
                    date_range: $('#date_range').val(),
                    invoice_no: $('#invoice_no').val(),
                    outlet_id: $('#outlet_id').val(),
                    delivery_point_id: $('#delivery_point_id').val(),
                    payment_method: $('#payment_method').val(),
                    product_id: $('#product_id').val(),
                    group_id: $('#group_id').val()
                };
                let queryString = $.param(params);
                window.location.href = url + '?' + queryString;
            });

            $('#reset-btn').on('click', function () {
                $('.filter-input').val('').trigger('change');
                if ($('#date_range').length > 0 && $('#date_range')[0]._flatpickr) {
                    $('#date_range')[0]._flatpickr.clear();
                }
                sessionStorage.removeItem('date_range_sale');
                sessionStorage.removeItem('invoice_no_sale');
                sessionStorage.removeItem('outlet_id_sale');
                sessionStorage.removeItem('delivery_point_id_sale');
                sessionStorage.removeItem('payment_method_sale');
                sessionStorage.removeItem('product_id_sale');
                sessionStorage.removeItem('group_id_sale');
                recallDatatable();
            });
        })
    </script>
@endpush
