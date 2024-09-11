@extends('layouts.app')
@section('title')
    Outlet Account List
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Master Data'=>'',
    'Outlet Account List'=>''
    ]
    @endphp
    <x-breadcrumb title='Outlet Account' :links="$links" />

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Outlet Account List</h3>
                            <div class="card-tools">
                                <a href="{{route('outlet-accounts.create')}}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus-circle"
                                       aria-hidden="true"></i> &nbsp;Add New

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
    <!-- page script -->
    <script>
        $(document).ready(function () {
            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('outlet-accounts.index') }}",
                },
                columns: [{
                    data: "DT_RowIndex",
                    title: "SL",
                    name: "DT_RowIndex",
                    searchable: false,
                    orderable: false
                },
                    {
                        data: "outlet.name",
                        title: "Outlet",
                        searchable: true
                    },
                    {
                        data: "coa.name",
                        title: "Chart of Account",
                        searchable: true
                    },
                    {
                        data: "status",
                        title: "Status",
                        searchable: true 
                    },
                    {
                        data: "created_at",
                        title: "Created at",
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
    </script>
@endpush
