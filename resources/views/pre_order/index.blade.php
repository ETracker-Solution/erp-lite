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
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title">Pre Order List</h3>
                            <div class="card-tools">
                                @can('sales-pre-order-entry')
                                    <a href="{{route('pre-orders.create')}}">
                                        <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                                  aria-hidden="true"></i> &nbsp;Add
                                            Pre Order
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
                                        <label for="">Filter By</label>
                                        <select name="filter_by" id="" class="form-control">
                                            <option value="">All</option>
                                            <option value="delivery_today">Todays Delivery</option>
                                            <option value="order_today">Todays Ordered</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">Status</label>
                                        <select name="status" id="" class="form-control">
                                            <option value="">All</option>
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label for="">Outlet</label>
                                        <select name="outlet_id" id="" class="form-control">
                                            <option value="">All</option>
                                            @foreach($outlets as $outlet)
                                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary mb-2" type="button" id="search-btn">Search</button>
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
                    url: "{{ route('pre-orders.index') }}",
                    data: function (d) {
                        d.filter_by = $('select[name="filter_by"]').val();
                        d.status = $('select[name="status"]').val();
                        d.outlet_id = $('select[name="outlet_id"]').val();
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
                        title: "Outlet",
                        searchable: true
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
                        title: "delivery date",
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

        $('#search-btn').on('click', function () {
            recallDatatable();
        });

        function recallDatatable() {
            $('#dataTable').DataTable().draw(true);
        }
    </script>
@endpush
