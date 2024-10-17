@extends('layouts.app')
@section('title')
    FG Requisition List
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'FG Requisition list'=>''
        ]
    @endphp
    <x-breadcrumb title='FG Requisition' :links="$links"/>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">FG Requisition List</h3>
                            <div class="card-tools">
                                @can('store-fg-create-fg-requisition')
                                    <a href="{{route('requisitions.create')}}">
                                        <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                                aria-hidden="true"></i> &nbsp;Add New
                                        </button>
                                    </a>
                                @endcan
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">Status</label>
                                        <select name="status" id="" class="form-control">
                                            <option value="">All</option>
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                            <option value="delivered">Delivered</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">From Date</label>
                                        <input type="date" name="from_date" id="from_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">To Date</label>
                                        <input type="date" name="to_date" id="to_date" class="form-control">
                                    </div>
                                </div>
{{--                                <div class="col-12">--}}
{{--                                    <button class="btn btn-primary" type="button" id="search-btn">Search</button>--}}
{{--                                </div>--}}
                            </div>
                            <hr>
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
@endsection
@section('js')
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection
@push('script')
    <!-- page script -->
    <script>
        $(document).ready(function () {

            if (sessionStorage.getItem('status')) {
                $('select[name="status"]').val(sessionStorage.getItem('status'));
            }
            if (sessionStorage.getItem('from_date')) {
                $('input[name="from_date"]').val(sessionStorage.getItem('from_date'));
            }
            if (sessionStorage.getItem('to_date')) {
                $('input[name="to_date"]').val(sessionStorage.getItem('to_date'));
            }

            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('requisitions.index') }}",
                    data: function (d) {
                        d.status = $('select[name="status"]').val();
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
                        data: "uid",
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
                        title: "From Store",
                        searchable: false,orderable: false,
                        "defaultContent": "Not Set"
                    },
                    {
                        data: "to_store.name",
                        title: "To Store",
                        searchable: false,orderable: false,
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
        })

        function recallDatatable() {
            $('#dataTable').DataTable().draw(true);
        }

        // On filter change, store the filter values in sessionStorage and recall the DataTable
        $('select[name="status"], input[name="from_date"], input[name="to_date"]').on('change', function () {
            sessionStorage.setItem('status', $('select[name="status"]').val());
            sessionStorage.setItem('from_date', $('input[name="from_date"]').val());
            sessionStorage.setItem('to_date', $('input[name="to_date"]').val());
            recallDatatable();
        });

        $('#search-btn').on('click', function () {
            recallDatatable();
        });
    </script>
@endpush
