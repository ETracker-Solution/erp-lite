@extends('layouts.app')
@section('title')
Receive Voucher List
@endsection

@section('content')
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Accounts Module'=>'',
    'General Accounts'=>'',
    'Receive Voucher List'=>''
    ]
    @endphp
    <x-breadcrumb title='Receive Voucher' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">All Receive Voucher List</h3>
                            <div class="card-tools">
                                <a href="{{route('receive-vouchers.create')}}">
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
                                    <label for="uid">RV No</label>
                                    <input type="text" id="uid" class="form-control filter-input" placeholder="RV No">
                                </div>
                                <div class="col-md-2">
                                    <label for="credit_account_id">Credit Account</label>
                                    <select id="credit_account_id" class="form-control filter-input select2">
                                        <option value="">Select Credit Account</option>
                                        @foreach($creditAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="debit_account_id">Debit Account</label>
                                    <select id="debit_account_id" class="form-control filter-input select2">
                                        <option value="">Select Debit Account</option>
                                        @foreach($debitAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="payee_name">Receiver Name</label>
                                    <input type="text" id="payee_name" class="form-control filter-input" placeholder="Receiver Name">
                                </div>
                                <div class="col-md-1">
                                    <button id="reset_filter" class="btn btn-warning btn-block">Reset</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="dataTable" class="table table-bordered table-hover">
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
<script>
    $(document).ready(function() {
        var table = $('#dataTable').DataTable({
            stateSave: true,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ route('receive-vouchers.index') }}",
                data: function (d) {
                    d.date_range = $('#date_range').val();
                    d.uid = $('#uid').val();
                    d.credit_account_id = $('#credit_account_id').val();
                    d.debit_account_id = $('#debit_account_id').val();
                    d.payee_name = $('#payee_name').val();
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
                    searchable: true
                },
                {
                    data: "uid",
                    title: "RV No",
                    searchable: true
                },
                {
                    data: "credit_account.name",
                    title: "Credit",
                    searchable: false
                },
                {
                    data: "debit_account.name",
                    title: "debit",
                    searchable: false,
                    "defaultContent":"Not Set"
                },
                {
                    data: "amount",
                    title: "Amount",
                    searchable: false
                },
                {
                    data: "payee_name",
                    title: "Receiver Name",
                    searchable: false
                },
                // {
                //     data: "created_at",
                //     title: "Created At",
                //     searchable: true
                // },
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
            $('.select2').val('').trigger('change');
            if ($('#date_range').length > 0) {
                $('#date_range')[0]._flatpickr.clear();
            }
            table.draw();
        });
    })
</script>

@endpush
