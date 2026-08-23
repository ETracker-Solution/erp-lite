@extends('layouts.app')
@section('title')
    Outlet Account List
@endsection
@section('content')
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Master Data'=>'',
    'Outlet Account List'=>''
    ]
    @endphp
    <x-breadcrumb title='Outlet Account' :links="$links" />

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Outlet Account List</h3>
                            <div class="card-tools">
                                <a href="{{route('outlet-accounts.create')}}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i> &nbsp;Add New
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('outlet-accounts.sync') }}" class="mb-3">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label>Sync missing accounts for outlet</label>
                                        <select name="outlet_id" class="form-control" required>
                                            <option value="">Select outlet…</option>
                                            @foreach($outlets as $outlet)
                                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-success">
                                            <i class="fa fa-sync"></i> Sync defaults
                                        </button>
                                    </div>
                                    <div class="col-md-5">
                                        <small class="text-muted">Creates missing Cash/Bkash/Nagad/Bank/Rocket/Upay ledgers, links, and POS payment configs.</small>
                                    </div>
                                </div>
                            </form>
                            <div class="row mb-2">
                                <div class="col-md-4">
                                    <select id="filterOutlet" class="form-control">
                                        <option value="">All outlets</option>
                                        @foreach($outlets as $outlet)
                                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="dataTable" class="table table-bordered table-hover"></table>
                            </div>
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
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            const table = $('#dataTable').DataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('outlet-accounts.index') }}",
                    data: function (d) {
                        d.outlet_id = $('#filterOutlet').val();
                    }
                },
                columns: [
                    {data: "DT_RowIndex", title: "SL", name: "DT_RowIndex", searchable: false, orderable: false},
                    {data: "outlet.name", title: "Outlet", searchable: true},
                    {data: "coa.name", title: "Chart of Account", searchable: true},
                    {data: "type", title: "Type", searchable: false, orderable: false},
                    {data: "status", title: "Status", searchable: true},
                    {data: "created_at", title: "Created at", searchable: true},
                    {data: "action", title: "Action", orderable: false, searchable: false},
                ],
            });

            $('#filterOutlet').on('change', function () {
                table.ajax.reload();
            });
        })
    </script>
@endpush
