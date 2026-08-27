@extends('layouts.app')

@section('title', 'Membership List')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Membership' => '',
        ];
    @endphp
    <x-breadcrumb title="Membership List" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Membership List</h3>
                            <div class="card-tools">
                                <a href="{{ route('memberships.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Add New
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="dataTable" class="table table-bordered table-hover table-sm"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
@endsection
@section('js')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            $('#dataTable').dataTable({
                stateSave: false,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('memberships.index') }}",
                },
                columns: [
                    {data: "DT_RowIndex", title: "SL", name: "DT_RowIndex", searchable: false, orderable: false},
                    {data: "membership_number", title: "Member No", searchable: true, defaultContent: "—"},
                    {data: "customer.name", name: "customer.name", title: "Customer", searchable: true, defaultContent: "—"},
                    {data: "customer.mobile", name: "customer.mobile", title: "Mobile", searchable: true, defaultContent: "—"},
                    {data: "member_type.name", name: "memberType.name", title: "Member Type", searchable: true, defaultContent: "—"},
                    {data: "point", title: "Point", searchable: false, className: "text-right"},
                    {data: "created_at", title: "Created At", searchable: false, orderable: false},
                    {data: "action", title: "Action", orderable: false, searchable: false},
                ],
            });
        });
    </script>
@endpush
