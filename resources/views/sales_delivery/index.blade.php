@extends('layouts.app')
@section('title', 'Sales Delivery List')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Sales Delivery' => '',
        ];
    @endphp
    <x-breadcrumb title="Sales Delivery" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Sales Delivery List</h3>
                            <div class="card-tools">
                                <a href="{{ route('sales-deliveries.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Make Delivery
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <div class="row mb-2">
                                <div class="col-md-3 form-group mb-2">
                                    <label class="small font-weight-bold mb-1" for="status">Status</label>
                                    <select name="status" id="status" class="form-control form-control-sm">
                                        <option value="">All</option>
                                        <option value="pending">Pending</option>
                                        <option value="delivered">Delivered</option>
                                    </select>
                                </div>
                                <div class="col-md-3 form-group mb-2">
                                    <label class="small font-weight-bold mb-1" for="from_date">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3 form-group mb-2">
                                    <label class="small font-weight-bold mb-1" for="to_date">To Date</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control form-control-sm">
                                </div>
                            </div>
                            <table id="dataTable" class="table table-bordered table-hover table-sm"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
@endsection
@section('js')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection
@push('script')
    <script>
        function recallDatatable() {
            $('#dataTable').DataTable().draw(true);
        }

        $(document).ready(function () {
            if (sessionStorage.getItem('sd_status')) {
                $('select[name="status"]').val(sessionStorage.getItem('sd_status'));
            }
            if (sessionStorage.getItem('sd_from_date')) {
                $('input[name="from_date"]').val(sessionStorage.getItem('sd_from_date'));
            }
            if (sessionStorage.getItem('sd_to_date')) {
                $('input[name="to_date"]').val(sessionStorage.getItem('sd_to_date'));
            }

            $('#dataTable').dataTable({
                stateSave: false,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('sales-deliveries.index') }}",
                    data: function (d) {
                        d.status = $('select[name="status"]').val();
                        d.from_date = $('input[name="from_date"]').val();
                        d.to_date = $('input[name="to_date"]').val();
                    }
                },
                columns: [
                    {data: "DT_RowIndex", title: "SL", name: "DT_RowIndex", searchable: false, orderable: false},
                    {data: "invoice_number", title: "Invoice No", searchable: true, defaultContent: "—"},
                    {data: "date", title: "Date", searchable: true, defaultContent: "—"},
                    {data: "outlet.name", name: "outlet.name", title: "Order From", searchable: true, defaultContent: "—"},
                    {data: "delivery_point.name", name: "deliveryPoint.name", title: "Delivery Point", searchable: true, defaultContent: "—"},
                    {data: "grand_total", title: "Grand Total", searchable: false, className: "text-right"},
                    {data: "due", title: "Due", searchable: false, className: "text-right", orderable: false},
                    {data: "status", title: "Status", searchable: false, orderable: false},
                    {data: "created_at", title: "Created At", searchable: false, orderable: false},
                    {data: "action", title: "Action", orderable: false, searchable: false},
                ],
            });

            $('select[name="status"], input[name="from_date"], input[name="to_date"]').on('change', function () {
                sessionStorage.setItem('sd_status', $('select[name="status"]').val());
                sessionStorage.setItem('sd_from_date', $('input[name="from_date"]').val());
                sessionStorage.setItem('sd_to_date', $('input[name="to_date"]').val());
                recallDatatable();
            });
        });
    </script>
@endpush
