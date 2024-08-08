@extends('layouts.app')
@section('title')
    Purchase Return List
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Purchase Return list'=>''
        ]
    @endphp
    <x-breadcrumb title='Goods Purchase Return' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title">Goods Purchase Return List</h3>
                            <div class="card-tools">
                                <a href="{{route('purchase-returns.create')}}">
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
                    url: "{{ route('purchase-returns.index') }}",
                },
                columns: [{
                    data: "DT_RowIndex",
                    title: "SL",
                    name: "DT_RowIndex",
                    searchable: false,
                    orderable: false
                },
                    {
                        data: "id",
                        title: "Purchase Return No",
                        searchable: true
                    },
                    {
                        data: "supplier.name",
                        title: "Supplier",
                        searchable: true
                    }, {
                        data: "store.name",
                        title: "Store",
                        searchable: true
                    },
                    {
                        data: "subtotal",
                        title: "Subtotal",
                        searchable: true
                    },
                    {
                        data: "vat",
                        title: "Vat Amount",
                        searchable: true
                    },
                    {
                        data: "net_payable",
                        title: "Net Payable",
                        searchable: true
                    },
                    {
                        data: "status",
                        title: "Status",
                        searchable: false
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
