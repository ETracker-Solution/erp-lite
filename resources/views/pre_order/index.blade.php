@extends('layouts.app')
@section('title')
    Pre Order List
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Pre Order list'=>''
        ]
    @endphp
    <x-breadcrumb title='Pre Order' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Pre Order List</h3>
                        </div>
                        <div class="card-body">
                            <div class="erp-filters mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filter_by">Filter By</label>
                                            <select name="filter_by" id="filter_by" class="form-control">
                                                <option value="">All</option>
                                                <option value="delivery_date">Delivery</option>
                                                <option value="order_date">Ordered</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">All</option>
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approved</option>
                                                <option value="delivered">Delivered</option>
                                                <option value="received">Received</option>
                                            </select>
                                        </div>
                                    </div>
                                    @if(!isset(auth()->user()?->employee?->outlet_id))
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="outlet_id">Outlet</label>
                                                <select name="outlet_id" id="outlet_id" class="form-control">
                                                    <option value="">All</option>
                                                    @foreach($outlets as $outlet)
                                                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="from_date">From Date</label>
                                            <input type="date" name="from_date" id="from_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="to_date">To Date</label>
                                            <input type="date" name="to_date" id="to_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end mb-3">
                                        <button class="btn btn-primary mr-2" type="button" id="search-btn">Search</button>
                                        <form method="GET" action="{{ route('pre-order.excel.export','xlsx') }}"
                                              id="excelForm" class="mb-0">
                                            @csrf
                                            <button class="btn btn-success" type="button" id="excel-btn">EXCEL</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive pre-order-table-wrap">
                                <table id="dataTable" class="table table-bordered table-hover table-sm w-100 mb-0">
                                    {{-- filled by DataTables --}}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
        <!-- Modal -->
        <div class="modal fade" id="deliverModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="POST" id="deliverForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="delivered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Deliver Pre Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Select Store</label>
                                <select name="factory_store" id="" class="form-control">
                                    <option value="">Choose One</option>
                                    @foreach($factoryStores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary do-deliver">Confirm Delivery</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="receiveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="POST" id="receiveForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="received">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Receive Pre Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Select Store</label>
                                <select name="outlet_store" id="" class="form-control">
                                    <option value="">Choose One</option>
                                    @foreach($outletStores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary do-receive">Confirm Receive</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="productionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="POST" id="productionForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="ready_to_delivery">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Production Pre Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="">Select Store</label>
                                <select name="factory_store" id="" class="form-control">
                                    <option value="">Choose One</option>
                                    @foreach($factoryStores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary do-production">Confirm Production</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection
@section('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
@endsection
@push('style')
    <style>
        /* Keep all columns visible: scroll inside the window instead of DataTables hiding them */
        .pre-order-table-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        #dataTable {
            width: 100% !important;
        }

        #dataTable th,
        #dataTable td {
            white-space: nowrap;
            vertical-align: middle;
        }

        .dataTables_wrapper .dataTables_scroll {
            width: 100%;
        }
    </style>
@endpush
@section('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
@endsection
@push('script')
    <!-- page script -->
    <script>

        let table;
        $(document).ready(function () {

            if (sessionStorage.getItem('filter_by')) {
                $('select[name="filter_by"]').val(sessionStorage.getItem('filter_by'));
            }
            if (sessionStorage.getItem('from_date')) {
                $('input[name="from_date"]').val(sessionStorage.getItem('from_date'));
            }
            if (sessionStorage.getItem('to_date')) {
                $('input[name="to_date"]').val(sessionStorage.getItem('to_date'));
            }
            if (sessionStorage.getItem('status')) {
                $('select[name="status"]').val(sessionStorage.getItem('status'));
            }
            if (sessionStorage.getItem('outlet_id')) {
                $('select[name="outlet_id"]').val(sessionStorage.getItem('outlet_id'));
            }

            @if(!(auth()->user()->employee->outlet_id ?? false))
            if (!$('select[name="filter_by"]').val()) {
                $('select[name="filter_by"]').val('');
            }
            if (!$('input[name="from_date"]').val()) {
                $('input[name="from_date"]').val('{{ now()->subMonths(3)->toDateString() }}');
            }
            if (!$('input[name="to_date"]').val()) {
                $('input[name="to_date"]').val('{{ now()->toDateString() }}');
            }
            @endif

            table = $('#dataTable').DataTable({
                stateSave: false,
                responsive: false,
                scrollX: true,
                autoWidth: false,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('pre-orders.index') }}",
                    data: function (d) {
                        d.filter_by = $('select[name="filter_by"]').val();
                        d.status = $('select[name="status"]').val();
                        d.outlet_id = $('select[name="outlet_id"]').val();
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
                        data: "order_number",
                        title: "Order No",
                        searchable: true,
                        "defaultContent": '<span class="text-danger">N/A</span>'
                    },
                    {
                        data: "customer.name",
                        title: "Customer",
                        searchable: true
                    }, {
                        data: "outlet.name",
                        title: "Order From",
                        searchable: true,
                        "defaultContent": '<span class="text-danger">N/A</span>'
                    }, {
                        data: "delivery_point.name",
                        name: "delivery_point.name",
                        title: "Delivery From",
                        "defaultContent": '<span class="text-danger">N/A</span>',
                        searchable: false
                    },
                    {
                        data: "subtotal",
                        title: "Subtotal",
                        searchable: true
                    },
                    {
                        data: "status",
                        title: "Status",
                        searchable: false
                    },
                    {
                        data: "delivery_date",
                        title: "Delivery Date",
                        searchable: true
                    },
                    {
                        data: "order_date",
                        title: "Order Date",
                        searchable: true
                    },
                    {
                        data: "due_amount",
                        title: "Due Amount",
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

            // Keep table sized to the content area on sidebar/window resize
            $(window).on('resize.preOrderTable', function () {
                if (table) {
                    table.columns.adjust();
                }
            });

            $('select[name="filter_by"], select[name="status"], select[name="outlet_id"], input[name="from_date"], input[name="to_date"]').on('change', function () {
                sessionStorage.setItem('filter_by', $('select[name="filter_by"]').val());
                sessionStorage.setItem('status', $('select[name="status"]').val());
                sessionStorage.setItem('outlet_id', $('select[name="outlet_id"]').val());
                sessionStorage.setItem('from_date', $('input[name="from_date"]').val());
                sessionStorage.setItem('to_date', $('input[name="to_date"]').val());
                recallDatatable();
            });
        });

        $('#search-btn').on('click', function () {
            recallDatatable();
        });

        function recallDatatable() {
            table.draw(true);
        }

        let preOrderId
        let submitUrl
        $('#deliverModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget)
            preOrderId = button.data('id')
            submitUrl = "/pre-orders.status-update/" + preOrderId
        })
        $('#productionModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget)
            preOrderId = button.data('id')
            submitUrl = "/pre-orders.status-update/" + preOrderId
        })
        $('#receiveModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget)
            preOrderId = button.data('id')
            submitUrl = "/pre-orders.status-update/" + preOrderId
        })
        $(document).on("click", ".do-deliver", function (e) {
            e.preventDefault()
            $("#deliverForm").attr('action', submitUrl).submit();
        })
        $(document).on("click", ".do-receive", function (e) {
            e.preventDefault()
            let form =$("#receiveForm")
            form.attr('action',submitUrl)
            $('.do-receive').attr('disabled', true)
            form.submit()
        })
        $(document).on("click", ".do-production", function (e) {
            e.preventDefault()
            let form =$("#productionForm")
            form.attr('action',submitUrl)
            $('.do-production').attr('disabled', true)
            form.submit()
        })

        $(document).on("click", "#excel-btn", function (e) {
            e.preventDefault();

            var form = $("#excelForm");
            form.find('input[data-export-filter="1"]').remove();

            var filters = {
                filter_by: $('select[name="filter_by"]').val() || '',
                status: $('select[name="status"]').val() || '',
                outlet_id: $('select[name="outlet_id"]').val() || '',
                from_date: $('input[name="from_date"]').val() || '',
                to_date: $('input[name="to_date"]').val() || ''
            };

            Object.keys(filters).forEach(function (key) {
                $('<input>', {
                    type: 'hidden',
                    name: key,
                    value: filters[key],
                    'data-export-filter': '1'
                }).appendTo(form);
            });

            form.trigger('submit');
        });

    </script>
@endpush
