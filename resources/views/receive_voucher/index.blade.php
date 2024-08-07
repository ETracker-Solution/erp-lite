@extends('layouts.app')
@section('title')
Receive Voucher List
@endsection

@section('content')
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Accounts Module'=>'',
    'General Accounts'=>'',
    'Receive Voucher List'=>''
    ]
    @endphp
    <x-breadcrumb title='Receive Voucher' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">All Receive Voucher List</h3>
                            <div class="card-tools">
                                <a href="{{route('receive-vouchers.create')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                              aria-hidden="true"></i> &nbsp;Add New
                                    </button>
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
<script>
    $(document).ready(function() {
        $('#dataTable').dataTable({
            stateSave: true,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ route('receive-vouchers.index') }}",
            },
            columns: [{
                    data: "DT_RowIndex",
                    title: "SL",
                    name: "DT_RowIndex",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "date",
                    title: "Date",
                    searchable: true
                },
                {
                    data: "uid",
                    title: "RV No",
                    searchable: true
                },
                {
                    data: "credit_account.name",
                    title: "Credit",
                    searchable: false
                },
                {
                    data: "debit_account.name",
                    title: "debit",
                    searchable: false,
                    "defaultContent":"Not Set"
                },
                {
                    data: "amount",
                    title: "Amount",
                    searchable: false
                },
                {
                    data: "payee_name",
                    title: "Receiver Name",
                    searchable: false
                },
                // {
                //     data: "created_at",
                //     title: "Created At",
                //     searchable: true
                // },
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
