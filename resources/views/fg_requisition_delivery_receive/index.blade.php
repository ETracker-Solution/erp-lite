@extends('layouts.app')
@section('title')
    FG Requisition Delivery Receive List
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'FG Requisition Delivery Receive list'=>''
        ]
    @endphp
    <x-breadcrumb title='FG Requisition Delivery Receive' :links="$links"/>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">FG Requisition Delivery Receive List</h3>
                            <div class="card-tools">
                                <a href="{{route('fg-delivery-receives.create')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                              aria-hidden="true"></i> &nbsp;Add New
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="date_range">Date Range</label>
                                    <input type="text" id="date_range" class="form-control filter-input" placeholder="YYYY-MM-DD to YYYY-MM-DD">
                                </div>
                                <div class="col-md-2">
                                    <label for="fgr_no">FGR No</label>
                                    <input type="text" id="fgr_no" class="form-control filter-input" placeholder="FGR No">
                                </div>
                                <div class="col-md-2">
                                    <label>From Store</label>
                                    <select id="from_store_id" class="form-control filter-input select2bs4">
                                        <option value="">Select From Store</option>
                                        @foreach($from_stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>To Store</label>
                                    <select id="to_store_id" class="form-control filter-input select2bs4">
                                        <option value="">Select To Store</option>
                                        @foreach($to_stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <button type="button" id="reset_btn" class="btn btn-secondary btn-block">Reset</button>
                                </div>
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
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('datepicker/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('datepicker/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
@endsection
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
                    url: "{{ route('fg-delivery-receives.index') }}",
                    data: function (d) {
                        d.date_range = $('#date_range').val();
                        d.fgr_no = $('#fgr_no').val();
                        d.from_store_id = $('#from_store_id').val();
                        d.to_store_id = $('#to_store_id').val();
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
                        data: "requisition_delivery.uid",
                        name: "requisitionDelivery.uid",
                        title: "FGR No",
                        searchable: true,
                        "defaultContent": "Not Set"
                    }, {
                        data: "date",
                        title: "Date",
                        searchable: true,
                        "defaultContent": "Not Set"
                    },
                    {
                        data: "from_store.name",
                        name: "fromStore.name",
                        title: "From Store",
                        searchable: true,
                        "defaultContent": "Not Set"
                    },
                    {
                        data: "to_store.name",
                        name: "toStore.name",
                        title: "To Store",
                        searchable: true,
                        "defaultContent": "Not Set"
                    },
                    {
                        data: "status",
                        title: "Status",
                        searchable: false, "defaultContent": "Not Set"
                    },
                    {
                        data: "created_at",
                        title: "Created At",
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

            $('#date_range').flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
                onClose: function(selectedDates, dateStr, instance) {
                    table.draw();
                }
            });

            $('.filter-input').on('change keyup', function () {
                table.draw();
            });

            $('#reset_btn').on('click', function () {
                $('.filter-input').val('').trigger('change');
                if ($('#date_range').length > 0 && $('#date_range')[0]._flatpickr) {
                    $('#date_range')[0]._flatpickr.clear();
                }
                table.draw();
            });
        })
    </script>
@endpush
