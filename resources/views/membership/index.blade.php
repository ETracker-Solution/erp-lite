@extends('layouts.app')

@section('title', 'Membership List')
@section('content')

@php
$links = [
'Home'=>route('dashboard'),
'Loyalty Module'=>'',
'Loyalty Entry'=>'',
'Membership list'=>''
]
@endphp
<x-breadcrumb title='Membership list' :links="$links" />
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">All Membership List</h3>
                        <div class="card-tools">
                            <a href="{{ route('memberships.create') }}"><button class="btn btn-sm btn-primary"><i
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
    $(document).ready(function () {
            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('memberships.index') }}",
                },
                columns: [{
                    data: "DT_RowIndex",
                    title: "SL",
                    name: "DT_RowIndex",
                    searchable: false,
                    orderable: false
                },
                    {
                        data: "customer.name",
                        title: "Customer",
                        searchable: true,
                        orderable: false,
                    },
                    {
                        data: "customer.mobile",
                        title: "Phone",
                        searchable: true,
                        orderable: false,
                    },
                    {
                        data: "member_type.name",
                        title: "Member Type",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: "point",
                        title: "Point",
                        searchable: false,
                        orderable: false,
                    },

                    {
                        data: "created_at",
                        title: "created at",
                        searchable: true,
                        "defaultContent": '<i class="text-danger">Not Set</i>'
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