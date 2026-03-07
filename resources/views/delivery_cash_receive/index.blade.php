@extends('layouts.app')

@section('title', 'Delivery Cash Receive')
@section('content')
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Accounts Module'=>'',
    'General Accounts'=>'',
    'Delivery Cash Receive'=>''
    ]
    @endphp
    <x-breadcrumb title='Delivery Cash Receive' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">All Delivery Cash Receive List</h3>
{{--                            <div class="card-tools">--}}
{{--                                <a href="{{route('delivery-cash-transfers.create')}}">--}}
{{--                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"--}}
{{--                                                                              aria-hidden="true"></i> &nbsp;Add New--}}
{{--                                    </button>--}}
{{--                                </a>--}}
{{--                            </div>--}}
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="date_range">Date Range</label>
                                    <input type="text" id="date_range" class="form-control filter-input flatpickr-range" placeholder="YYYY-MM-DD to YYYY-MM-DD">
                                </div>
                                <div class="col-md-2">
                                    <label for="invoice_number">Delivery Invoice</label>
                                    <input type="text" id="invoice_number" class="form-control filter-input" placeholder="Invoice No">
                                </div>
                                <div class="col-md-3">
                                    <label for="from_account">Transfer From</label>
                                    <input type="text" id="from_account" class="form-control filter-input" placeholder="Account Name">
                                </div>
                                <div class="col-md-3">
                                    <label for="to_account">Transfer To</label>
                                    <input type="text" id="to_account" class="form-control filter-input" placeholder="Account Name">
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
                url: "{{ route('delivery-cash-receives.index') }}",
                data: function (d) {
                    d.date_range = $('#date_range').val();
                    d.invoice_number = $('#invoice_number').val();
                    d.from_account = $('#from_account').val();
                    d.to_account = $('#to_account').val();
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
                    data: "invoice_number",
                    title: "Delivery Invoice",
                    searchable: true
                },
                {
                    data: "credit_account.name",
                    title: "Transfer From",
                    searchable: false
                },
                {
                    data: "debit_account.name",
                    title: "Transfer To",
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
