@extends('layouts.app')
@section('title')
    Customer Receive Voucher List
@endsection

@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Accounts Module'=>'',
        'Customer Receive Voucher List'=>''
        ]
    @endphp
    <x-breadcrumb title='Customer Receive Voucher' :links="$links"/>
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
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="crv_no">CRV No</label>
                                <input type="text" id="crv_no" class="form-control filter-input" placeholder="CRV No">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="invoice_no_filter">Invoice No</label>
                                <input type="text" id="invoice_no_filter" class="form-control filter-input" placeholder="Invoice No">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="received_to">Received To</label>
                                <select id="received_to" class="form-control filter-input select2">
                                    <option value="">All Accounts</option>
                                    @foreach($paymentAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
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
                            <h3 class="card-title">All Customer Receive Voucher List</h3>
                            <div class="card-tools">
                                <a href="{{route('customer-receive-vouchers.create')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                              aria-hidden="true"></i> &nbsp;Add New
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="dataTable"
                                   class="table table-bordered table-hover">
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
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
@endsection
@push('script')
    <script>
        function recallDatatable() {
            $('#dataTable').DataTable().draw(true);
        }

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            if (sessionStorage.getItem('date_range_crv')) {
                $('#date_range').val(sessionStorage.getItem('date_range_crv'));
            }
            if (sessionStorage.getItem('crv_no_crv')) {
                $('#crv_no').val(sessionStorage.getItem('crv_no_crv'));
            }
            if (sessionStorage.getItem('invoice_no_crv')) {
                $('#invoice_no_filter').val(sessionStorage.getItem('invoice_no_crv'));
            }
            if (sessionStorage.getItem('received_to_crv')) {
                $('#received_to').val(sessionStorage.getItem('received_to_crv')).trigger('change');
            }

            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('customer-receive-vouchers.index') }}",
                    data: function (d) {
                        d.date_range = $('#date_range').val();
                        d.crv_no = $('#crv_no').val();
                        d.invoice_no = $('#invoice_no_filter').val();
                        d.received_to = $('#received_to').val();
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
                        data: "date",
                        title: "Date",
                        name: "date",
                        searchable: true
                    },
                    {
                        data: "uid",
                        title: "CRV No",
                        name: "uid",
                        searchable: true
                    },
                    {
                        data: "customer.name",
                        title: "Customer",
                        name: "customer.name",
                        searchable: true
                    },
                    {
                        data: "invoice_no",
                        title: "Invoice No",
                        name: "invoice_no",
                        searchable: true,
                        orderable: false
                    },
                    {
                        data: "debit_account.name",
                        title: "Received To",
                        name: "debitAccount.name",
                        searchable: true,
                        defaultContent: "N/A"
                    },
                    {
                        data: "amount",
                        title: "Amount",
                        name: "amount",
                        searchable: false
                    },
                    {
                        data: "settle_discount",
                        title: "Settle Discount",
                        name: "settle_discount",
                        searchable: false
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
                sessionStorage.setItem('date_range_crv', $('#date_range').val());
                sessionStorage.setItem('crv_no_crv', $('#crv_no').val());
                sessionStorage.setItem('invoice_no_crv', $('#invoice_no_filter').val());
                sessionStorage.setItem('received_to_crv', $('#received_to').val());
                recallDatatable();
            });

            $('#reset-btn').on('click', function () {
                $('.filter-input').val('');
                $('.select2').val('').trigger('change');
                
                if ($('#date_range').length > 0 && $('#date_range')[0]._flatpickr) {
                    $('#date_range')[0]._flatpickr.clear();
                }
                sessionStorage.removeItem('date_range_crv');
                sessionStorage.removeItem('crv_no_crv');
                sessionStorage.removeItem('invoice_no_crv');
                sessionStorage.removeItem('received_to_crv');
                recallDatatable();
            });
        })
    </script>
@endpush
