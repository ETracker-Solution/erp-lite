@extends('layouts.app')

@section('title', 'Fund Transfer Voucher')
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Accounts Module'=>'',
        'General Accounts'=>'',
        'Fund Transfer Voucher'=>''
        ]
    @endphp
    <x-breadcrumb title='Fund Transfer Voucher' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @can('accounts-ft-voucher-filter')
                        <div class="col-lg-12 col-md-12">
                            <div class="mb-2 card">
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form method="POST" id="submitForm">
                                            @csrf
                                        <div class="row align-items-end">
                                            <div class="col-md-2 form-group mb-0">
                                                <label for="fp-range" class="font-weight-bold">DATE RANGE</label>
                                                <input type="text" id="fp-range" class="form-control flatpickr-range filter-input"
                                                       placeholder="YYYY-MM-DD to YYYY-MM-DD" name="date_range"/>
                                            </div>
                                            <!-- <div class="form-group col-md-2 mb-0">
                                                <label for="outlet_id" class="font-weight-bold">Outlet</label>
                                                <select class="form-control select2 filter-input" name="outlet_id" id="outlet_id">
                                                    <option value="">All</option>
                                                    @foreach ($outlets as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div> -->

                                            <div class="form-group col-md-2 mb-0">
                                                <label for="from_account_id" class="font-weight-bold">From Account</label>
                                                <select class="form-control select2 filter-input" name="from_account_id" id="from_account_id">
                                                    <option value="">All</option>
                                                    @foreach ($from_accounts as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group col-md-2 mb-0">
                                                <label for="to_account_id" class="font-weight-bold">To Account</label>
                                                <select class="form-control select2 filter-input" name="to_account_id" id="to_account_id">
                                                    <option value="">All</option>
                                                    @foreach ($to_accounts as $row)
                                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group col-md-2 mb-0">
                                                <button type="button" id="reset_filter" class="btn btn-warning btn-block"><i class="fa fa-sync"></i> Reset</button>
                                            </div>
                                            <div class="form-group col-md-2 mb-0">
                                                <button type="button" id="export_excel" class="btn btn-success btn-block"><i class="fa fa-file-excel"></i> Export</button>
                                            </div>
                                            
                                        </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endcan
                    @if(\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id)
                        <div class="mb-2 card">
                            <div class="card-content collapse show">
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div style="border: 1px solid #000" class="p-1">
                                                @foreach($outlet_accounts as $outlet_account)
                                                    <span class="badge badge-success p-1 m-1">{{$outlet_account['name']}} :   {{$outlet_account['balance']}} BDT</span>
                                                @endforeach
                                            </div>


                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    @endif
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">All Fund Transfer Voucher List</h3>
                            <div class="card-tools" style="display: ruby">
                                @if(auth()->user()->employee->user_of != 'outlet')
                                    <button class="btn btn-sm btn-danger" id="receiveReportButton"><i class="fas fa-file-pdf"
                                                                              aria-hidden="true"></i> &nbsp;Receive Report
                                    </button>
                                @endif
                                <a href="{{route('fund-transfer-vouchers.create')}}">
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
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/css/plugins/forms/pickers/form-pickadate.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker') }}/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@push('style')
<style>
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 2px) !important;
    }
</style>
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
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            if (sessionStorage.getItem('outlet_id_ftv')) {
                $('#outlet_id').val(sessionStorage.getItem('outlet_id_ftv')).trigger('change.select2');
            }
            if (sessionStorage.getItem('from_account_id_ftv')) {
                $('#from_account_id').val(sessionStorage.getItem('from_account_id_ftv')).trigger('change.select2');
            }
            if (sessionStorage.getItem('to_account_id_ftv')) {
                $('#to_account_id').val(sessionStorage.getItem('to_account_id_ftv')).trigger('change.select2');
            }
            if (sessionStorage.getItem('date_range_ftv')) {
                $('#fp-range').val(sessionStorage.getItem('date_range_ftv'));
            }

            let table = $('#dataTable').DataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('fund-transfer-vouchers.index') }}",
                    data: function (d) {
                        d.outlet_id = $('#outlet_id').val();
                        d.from_account_id = $('#from_account_id').val();
                        d.to_account_id = $('#to_account_id').val();
                        d.date_range = $('#fp-range').val();
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
                        searchable: true,
                        orderable: false
                    },
                    {
                        data: "uid",
                        title: "FTV No",
                        name: "uid",
                        searchable: true,
                        orderable: false
                    },
                    {
                        data: "credit_account.name",
                        title: "Transfer From",
                        name: "creditAccount.name",
                        searchable: false,
                        defaultContent: '-',
                        orderable: false
                    },
                    {
                        data: "debit_account.name",
                        title: "Transfer To",
                        name: "debitAccount.name",
                        searchable: false,
                        defaultContent: '-',
                        orderable: false
                    },
                    {
                        data: "amount",
                        title: "Amount",
                        name: "amount",
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: "status",
                        title: "Status",
                        name: "status",
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

            $('.filter-input').on('change keyup', function () {
                if ($(this).attr('name') === 'date_range') {
                    sessionStorage.setItem('date_range_ftv', $(this).val());
                } else {
                    sessionStorage.setItem($(this).attr('name') + '_ftv', $(this).val());
                }
                recallDatatable();
            });

            $('#reset_filter').click(function () {
                sessionStorage.removeItem('date_range_ftv');
                sessionStorage.removeItem('outlet_id_ftv');
                sessionStorage.removeItem('from_account_id_ftv');
                sessionStorage.removeItem('to_account_id_ftv');
                
                $('#fp-range').val('');
                $('#outlet_id').val('').trigger('change.select2');
                $('#from_account_id').val('').trigger('change.select2');
                $('#to_account_id').val('').trigger('change.select2');
                
                if ($('#fp-range').length > 0 && $('#fp-range')[0]._flatpickr) {
                    $('#fp-range')[0]._flatpickr.clear();
                }
                
                recallDatatable();
            });

            $('#export_excel').on('click', function () {
                let url = "{{ route('fund-transfer-vouchers.export') }}";
                let params = {
                    date_range: $('#fp-range').val(),
                    outlet_id: $('#outlet_id').val(),
                    from_account_id: $('#from_account_id').val(),
                    to_account_id: $('#to_account_id').val()
                };
                let queryString = $.param(params);
                window.location.href = url + '?' + queryString;
            });

            $('#receiveReportButton').on('click', function () {
                $('#submitForm').attr('action', "{{ route('fund-transfer-vouchers.receive.report') }}").submit();
            });

            function recallDatatable() {
                table.draw(true);
            }
        });
    </script>

@endpush
