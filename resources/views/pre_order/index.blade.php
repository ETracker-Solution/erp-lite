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
                                {{--                                @can('sales-pre-order-entry')--}}
                                {{--                                    <a href="{{route('pre-orders.create')}}">--}}
                                {{--                                        <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"--}}
                                {{--                                                                                  aria-hidden="true"></i> &nbsp;Add New--}}
                                {{--                                        </button>--}}
                                {{--                                    </a>--}}
                                {{--                                @endcan--}}
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
                                            <option value="delivered">Delivered</option>
                                            <option value="received">Received</option>
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
                        title: "Order From",
                        searchable: true,
                        "defaultContent": '<span class="text-danger">N/A</span>'
                    }, {
                        data: "delivery_point.name",
                        title: "Delivery From",
                        "defaultContent": '<span class="text-danger">N/A</span>',
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
            $("#receiveForm").attr('action', submitUrl).submit();
        })
        $(document).on("click", ".do-production", function (e) {
            e.preventDefault()
            $("#productionForm").attr('action', submitUrl).submit();
        })
    </script>
@endpush
