@extends('layouts.app')
@section('title')
    Purchase List
@endsection
@section('content')
        @php
            $links = [
                'Home' => route('dashboard'),
                'Pre-define Production Recipe list' => ''
            ]
        @endphp
                <x-breadcrumb title='Pre-define Recipe' :links="$links" />
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-info">
                                        <h3 class="card-title">Pre-define Production Recipe List</h3>
                                        <div class="card-tools">
                                            <a href="{{route('production-recipes.create')}}">
                                                <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                        aria-hidden="true"></i> &nbsp;Add New
                                                </button>
                                            </a>
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
                                url: "{{ route('production-recipes.index') }}",
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
                                title: "Recipe No",
                                searchable: true
                            },
                            {
                                data: "item.name",
                                title: "Product",
                                searchable: true
                            },
                            {
                                data: "created_at",
                                title: "Date",
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