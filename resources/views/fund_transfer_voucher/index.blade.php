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
                    <div class="erp-filters">
                        <form method="POST" id="submitForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label for="fp-range">Date Range</label>
                                    <input type="text" id="fp-range"
                                           class="form-control flatpickr-range"
                                           placeholder="Pick day, or same day twice, or from–to" name="date_range"
                                           value="{{ now()->startOfMonth()->format('Y-m-d') . ' to ' . now()->format('Y-m-d') }}"/>
                                </div>
                                @can('accounts-ft-voucher-filter')
                                    <div class="form-group col-md-3">
                                        <label for="outlet_id">Outlet</label>
                                        <select class="form-control select2" name="outlet_id" id="outlet_id">
                                            <option value="" selected>All</option>
                                            @foreach ($outlets as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="account_id">From Account</label>
                                        <select class="form-control select2" name="account_id"
                                                id="account_id">
                                            <option value="" selected>All</option>
                                            @foreach ($accounts as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="to_account_id">To Account</label>
                                        <select class="form-control select2" name="to_account_id"
                                                id="to_account_id">
                                            <option value="" selected>All</option>
                                            @foreach ($toAccounts as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endcan
                            </div>
                        </form>
                    </div>
                    @if(\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id)
                        <div class="card mb-2">
                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="erp-balance-table mb-0">
                                        <thead>
                                        <tr>
                                            <th class="is-available">Available</th>
                                            <th class="is-transit">In Transit</th>
                                            <th class="is-transfer">Transferable</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($outlet_accounts as $outlet_account)
                                            <tr>
                                                <td>
                                                    <span class="erp-balance-chip available">
                                                        <span class="name">{{ $outlet_account['name'] }}</span>
                                                        <span class="amt">{{ number_format($outlet_account['balance'], 2) }} BDT</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="erp-balance-chip transit">
                                                        <span class="name">{{ $outlet_account['name'] }}</span>
                                                        <span class="amt">{{ number_format($outlet_account['pending'], 2) }} BDT</span>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="erp-balance-chip transfer">
                                                        <span class="name">{{ $outlet_account['name'] }}</span>
                                                        <span class="amt">{{ number_format($outlet_account['balance'] - $outlet_account['pending'], 2) }} BDT</span>
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Fund Transfer Vouchers</h3>
                            <div class="card-tools">
                                @if(auth()->user()->employee->user_of != 'outlet')
                                    <button class="btn btn-sm btn-danger" id="receiveReportButton" type="button"><i
                                                class="fas fa-file-pdf"
                                                aria-hidden="true"></i> &nbsp;Receive Report
                                    </button>
                                @endif
                                <a href="{{route('fund-transfer-vouchers.create')}}">
                                    <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-plus-circle"
                                                                              aria-hidden="true"></i> &nbsp;Add New
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="ftvTable"
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
    <script>
        $(document).ready(function () {
            if (sessionStorage.getItem('outlet_id')) {
                $('select[name="outlet_id"]').val(sessionStorage.getItem('outlet_id'));
            }
            if (sessionStorage.getItem('account_id')) {
                $('select[name="account_id"]').val(sessionStorage.getItem('account_id'));
            }
            if (sessionStorage.getItem('to_account_id')) {
                $('select[name="to_account_id"]').val(sessionStorage.getItem('to_account_id'));
            }

            const defaultFrom = @json(now()->startOfMonth()->format('Y-m-d'));
            const defaultTo = @json(now()->format('Y-m-d'));
            const defaultDateRange = defaultFrom + ' to ' + defaultTo;
            let lastAppliedRange = defaultDateRange;

            // Always open on current month (ignore old wide sessionStorage ranges).
            sessionStorage.setItem('date_range', defaultDateRange);
            $('input[name="date_range"]').val(defaultDateRange);

            function applyFtvDateRange(selectedDates, dateStr, instance) {
                if (!selectedDates || !selectedDates.length || !instance) {
                    return;
                }

                var fromDate = selectedDates[0];
                var toDate = selectedDates.length > 1 ? selectedDates[1] : selectedDates[0];
                var fromStr = instance.formatDate(fromDate, 'Y-m-d');
                var toStr = instance.formatDate(toDate, 'Y-m-d');

                // One click, or same day clicked twice → single-day filter.
                if (selectedDates.length === 1 || fromStr === toStr) {
                    instance.setDate([fromDate, fromDate], false);
                    dateStr = fromStr + ' to ' + fromStr;
                } else {
                    dateStr = fromStr + ' to ' + toStr;
                }

                // Flatpickr already writes dateStr into the input before onChange,
                // so compare against last applied filter (not the input value).
                if (lastAppliedRange === dateStr) {
                    return;
                }

                lastAppliedRange = dateStr;
                $('input[name="date_range"]').val(dateStr);
                sessionStorage.setItem('date_range', dateStr);
                recallDatatable();
            }

            // Re-init range picker ourselves so we control format + when to reload.
            // Global form-pickers.js already attached; destroy that instance first.
            const rangeInput = document.getElementById('fp-range');
            if (rangeInput && rangeInput._flatpickr) {
                rangeInput._flatpickr.destroy();
            }
            if (rangeInput && typeof flatpickr !== 'undefined') {
                flatpickr(rangeInput, {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    defaultDate: [defaultFrom, defaultTo],
                    onChange: function (selectedDates, dateStr, instance) {
                        // Two clicks (including same day twice) complete the range.
                        if (selectedDates.length === 2) {
                            applyFtvDateRange(selectedDates, dateStr, instance);
                        }
                    },
                    onClose: function (selectedDates, dateStr, instance) {
                        // One click then close → same-day range.
                        if (selectedDates.length === 1) {
                            applyFtvDateRange(selectedDates, dateStr, instance);
                        }
                    }
                });
            }

            if ($.fn.select2) {
                $('.select2').select2({width: '100%'});
            }

            $('#ftvTable').dataTable({
                stateSave: false,
                responsive: true,
                serverSide: true,
                processing: true,
                pageLength: 25,
                ajax: {
                    url: "{{ route('fund-transfer-vouchers.index') }}",
                    data: function (d) {
                        d.outlet_id = $('select[name="outlet_id"]').val() || '';
                        d.account_id = $('select[name="account_id"]').val() || '';
                        d.to_account_id = $('select[name="to_account_id"]').val() || '';
                        d.date_range = $('input[name="date_range"]').val() || '';
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
                        searchable: true,
                        orderable: false
                    },
                    {
                        data: "uid",
                        title: "FTV No",
                        searchable: true,
                        orderable: false
                    },
                    {
                        data: "credit_account.name",
                        title: "Transfer From",
                        searchable: false,
                        defaultContent: '-',
                        orderable: false
                    },
                    {
                        data: "debit_account.name",
                        title: "Transfer To",
                        searchable: false,
                        defaultContent: '-',
                        orderable: false
                    },
                    {
                        data: "amount",
                        title: "Amount",
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: "status",
                        title: "Status",
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: "action",
                        title: "Action",
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $(document).on('click', '.receive-ftv-btn', function (event) {
                event.preventDefault();
                const element = this;
                const href = element.getAttribute('href');
                element.classList.add('disabled');
                element.style.pointerEvents = "none";
                element.style.opacity = "0.6";
                window.location.href = href;
            });
        });

        $('#outlet_id').on('change', function () {
            sessionStorage.setItem('outlet_id', $('select[name="outlet_id"]').val());
            recallDatatable();
        });
        $('#account_id').on('change', function () {
            sessionStorage.setItem('account_id', $('select[name="account_id"]').val());
            recallDatatable();
        });
        $('#to_account_id').on('change', function () {
            sessionStorage.setItem('to_account_id', $('select[name="to_account_id"]').val());
            recallDatatable();
        });
        $('#receiveReportButton').on('click', function () {
            $('#submitForm').attr('action', "{{ route('fund-transfer-vouchers.receive.report') }}").submit()
        });

        function recallDatatable() {
            if ($.fn.DataTable.isDataTable('#ftvTable')) {
                $('#ftvTable').DataTable().ajax.reload(null, false);
            }
        }
    </script>

@endpush
