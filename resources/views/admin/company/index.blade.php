@extends('admin.layouts.app')
@section('title')
Company List
@endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    @php
    $links = [
    'Home'=>route('admin.admin_dashboard'),
    'Company list'=>''
    ]
    @endphp
    <x-breadcrumb title='Company' :links="$links" />
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title" style="color:#115548;">Company List</h3>
                        {{-- <div class="card-tools">
                            <a href="{{route('admin.companies.create')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp;Add Brand</button></a>
                        </div> --}}
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="dataTable" class="datatables-basic table">
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
<script>
    $(document).ready(function() {
        $('#dataTable').dataTable({
            stateSave: true,
            responsive: true,
            serverSide: true,
            processing: true,
            ajax: '{{route('admin.companies.index')}}',
            columns: [{
                    data: "DT_RowIndex",
                    title: "#",
                    name: "DT_RowIndex",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "name",
                    title: "Name",
                    searchable: true
                },
                {
                    data: "email",
                    title: "Email",
                    searchable: true
                },
                {
                    data: "phone",
                    title: "Phone",
                    searchable: true
                },
                {
                    data: "address",
                    title: "Address",
                    searchable: true
                },
                {
                    data: "no_of_employee",
                    title: "Number of Employee",
                    searchable: true
                },
                {
                    data: "type",
                    title: "Type",
                    searchable: true
                },
                {
                    data: "status",
                    title: "Status",
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
