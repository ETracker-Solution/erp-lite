@extends('layouts.app')

@section('title', 'Payment Voucher')
@push('style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/plugins/forms/pickers/form-flat-pickr.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/plugins/forms/pickers/form-pickadate.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('admin')}}/app-assets/css/core/menu/menu-types/vertical-menu.css">
@endpush
@section('content')

    @php
    $links = [
    'Home'=>route('dashboard'),
    'Accounts Module'=>'',
    'General Accounts'=>'',
    'Payment Voucher'=>''
    ]
    @endphp
    <x-breadcrumb title='Payment Voucher' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">All Payment Voucher List</h3>
                            <div class="card-tools">
                                <a href="{{route('payment-vouchers.create')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                              aria-hidden="true"></i> &nbsp;Add New
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" class="form-control filter-input" placeholder="Start Date">
                                </div>
                                <div class="col-md-2">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" class="form-control filter-input" placeholder="End Date">
                                </div>
                                <div class="col-md-2">
                                    <label for="uid">PV No</label>
                                    <input type="text" id="uid" class="form-control filter-input" placeholder="PV No">
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
                                    <label for="credit_account_id">Payment Account</label>
                                    <select id="credit_account_id" class="form-control filter-input select2">
                                        <option value="">Select Payment Account</option>
                                        @foreach($creditAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label><br>
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
@endsection
@push('style')

@endpush
@section('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
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
                url: "{{ route('payment-vouchers.index') }}",
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.uid = $('#uid').val();
                    d.debit_account_id = $('#debit_account_id').val();
                    d.credit_account_id = $('#credit_account_id').val();
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
                    title: "PV No",
                    searchable: true
                },
                {
                    data: "debit_account.name",
                    title: "Debit",
                    searchable: false
                },
                {
                    data: "cash_bank_account.name",
                    title: "Payment",
                    searchable: false
                },
                {
                    data: "amount",
                    title: "Amount",
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

        $('.filter-input').on('change keyup', function () {
            table.draw();
        });

        $('#reset_filter').click(function () {
            $('.filter-input').val('');
            $('.select2').val('').trigger('change');
            table.draw();
        });
    })
</script>

@endpush
