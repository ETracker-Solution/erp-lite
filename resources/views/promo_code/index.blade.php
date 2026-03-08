@extends('layouts.app')

@section('title', 'Promo Code List')
@section('content')

@php
$links = [
'Home'=>route('dashboard'),
'Loyalty Module'=>'',
'Loyalty Entry'=>'',
'Promo Code list'=>''
]
@endphp
<x-breadcrumb title='Promo Code list' :links="$links" />
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="row align-items-end mb-3">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="date_range">Date Range</label>
                            <input type="text" id="date_range" class="form-control filter-input flatpickr-range" placeholder="YYYY-MM-DD to YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="code">Code</label>
                            <input type="text" id="code" class="form-control filter-input" placeholder="Promo Code">
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
                        <h3 class="card-title">All Promo Code List</h3>
                        <div class="card-tools">
                            <a href="{{ route('promo-codes.create') }}"><button class="btn btn-sm btn-primary"><i
                                        class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp;Add New</button></a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive">
                        <table id="dataTable" class="table table-bordered table-hover">
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
    function recallDatatable() {
        $('#dataTable').DataTable().draw(true);
    }

    $(document).ready(function () {
            if (sessionStorage.getItem('date_range_promo')) {
                $('#date_range').val(sessionStorage.getItem('date_range_promo'));
            }
            if (sessionStorage.getItem('code_promo')) {
                $('#code').val(sessionStorage.getItem('code_promo'));
            }

            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('promo-codes.index') }}",
                    data: function (d) {
                        d.date_range = $('#date_range').val();
                        d.code = $('#code').val();
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        title: "SL",
                        name: "DT_RowIndex",
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: "code",
                        title: "code",
                        name: "code",
                        searchable: true
                    },
                    {
                        data: "start_date",
                        title: "start date",
                        name: "start_date",
                        orderable: false,
                        "defaultContent": "Not Set"
                    },
                    {
                        data: "end_date",
                        title: "end date",
                        name: "end_date",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: "discount",
                        title: "Discount",
                        name: "discount",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: "created_at",
                        title: "created at",
                        name: "created_at",
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

            $('.flatpickr-range').flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
            });

            $('.filter-input').on('keyup change', function () {
                sessionStorage.setItem('date_range_promo', $('#date_range').val());
                sessionStorage.setItem('code_promo', $('#code').val());
                recallDatatable();
            });

            $('#reset-btn').on('click', function () {
                $('.filter-input').val('');
                if ($('#date_range').length > 0 && $('#date_range')[0]._flatpickr) {
                    $('#date_range')[0]._flatpickr.clear();
                }
                sessionStorage.removeItem('date_range_promo');
                sessionStorage.removeItem('code_promo');
                recallDatatable();
            });
        })
</script>

@endpush