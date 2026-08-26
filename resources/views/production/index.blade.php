@extends('layouts.app')
@section('title', 'FG Production List')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'FG Production list' => '',
        ];
    @endphp
    <x-breadcrumb title="FG Production" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">FG Production List</h3>
                            <div class="card-tools">
                                <a href="{{ route('productions.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <div class="row mb-2">
                                <div class="col-md-3 form-group mb-2">
                                    <label for="fp-range" class="font-weight-bold small mb-1">DATE RANGE</label>
                                    <input type="text" id="fp-range" class="form-control flatpickr-range"
                                           placeholder="YYYY-MM-DD to YYYY-MM-DD" name="date_range"/>
                                </div>
                                <div class="col-md-3 form-group mb-2 d-flex align-items-end">
                                    <form method="GET" action="{{ route('fg.production.export', 'xlsx') }}"
                                          id="excelForm" class="mb-0">
                                        @csrf
                                        <button class="btn btn-success" type="button" id="excel-btn">EXCEL</button>
                                    </form>
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
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css"
          href="{{ asset('datepicker/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
@endsection
@section('js')
    <script src="{{ asset('datepicker/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('datepicker/app-assets/js/scripts/forms/pickers/form-pickers.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            if (sessionStorage.getItem('production_date_range')) {
                $('input[name="date_range"]').val(sessionStorage.getItem('production_date_range'));
            }

            $('#dataTable').dataTable({
                stateSave: false,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('productions.index') }}",
                    data: function (d) {
                        d.date_range = $('input[name="date_range"]').val();
                    }
                },
                columns: [
                    {data: "DT_RowIndex", title: "SL", name: "DT_RowIndex", searchable: false, orderable: false},
                    {data: "uid", title: "FGP No", searchable: true, defaultContent: "—"},
                    {data: "date", title: "Date", searchable: true, defaultContent: "—"},
                    {data: "batch.batch_no", name: "batch.batch_no", title: "Batch", searchable: true, defaultContent: "—"},
                    {data: "factory.name", name: "factory.name", title: "Factory", searchable: true, defaultContent: "—"},
                    {data: "store.name", name: "store.name", title: "FG Store", searchable: true, defaultContent: "—"},
                    {data: "total_quantity", title: "Qty", searchable: false, className: "text-right", defaultContent: "0.00"},
                    {data: "subtotal", title: "Subtotal", searchable: false, className: "text-right", defaultContent: "0.00"},
                    {data: "status", title: "Status", searchable: false, orderable: false},
                    {data: "created_at", title: "Created At", searchable: false, orderable: false},
                    {data: "action", title: "Action", orderable: false, searchable: false},
                ],
            });
        });

        $('#fp-range').on('change', function () {
            sessionStorage.setItem('production_date_range', $('input[name="date_range"]').val());
            $('#dataTable').DataTable().draw(true);
        });

        $(document).on("click", "#excel-btn", function (e) {
            e.preventDefault();
            let form = $("#excelForm");
            let date_range = $('input[name="date_range"]');
            form.find('input[name="date_range"]').remove();
            form.append($('<input>', {type: 'hidden', name: 'date_range', value: date_range.val()}));
            form.submit();
        });
    </script>
@endpush
