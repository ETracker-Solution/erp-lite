@extends('layouts.app')

@section('title', 'Delivery Cash Receive')
@section('content')
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Accounts Module'=>'',
    'General Accounts'=>'',
    'Delivery Cash Receive'=>''
    ]
    @endphp
    <x-breadcrumb title='Delivery Cash Receive' :links="$links"/>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">All Delivery Cash Receive List</h3>
{{--                            <div class="card-tools">--}}
{{--                                <a href="{{route('delivery-cash-transfers.create')}}">--}}
{{--                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"--}}
{{--                                                                              aria-hidden="true"></i> &nbsp;Add New--}}
{{--                                    </button>--}}
{{--                                </a>--}}
{{--                            </div>--}}
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
                url: "{{ route('delivery-cash-receives.index') }}",
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
                    data: "invoice_number",
                    title: "Delivery Invoice",
                    searchable: true
                },
                {
                    data: "credit_account.name",
                    title: "Transfer From",
                    searchable: false
                },
                {
                    data: "debit_account.name",
                    title: "Transfer To",
                    searchable: false
                },
                {
                    data: "amount",
                    title: "Amount",
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
