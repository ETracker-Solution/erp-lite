@extends('layouts.app')
@section('title', 'Supplier Due List')
@section('content')
    @php
        $links = [
            'Home'=>route('dashboard'),
            'Accounts Module'=>'',
            'Supplier Due List'=>'',
        ]
    @endphp
    <x-breadcrumb title='Supplier Due List' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Supplier Due List</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm"
                                   id="supplierDueTable">
                                <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Supplier Name</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                    <th>Total Due</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(function () {
            $('#supplierDueTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('supplier-dues.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'mobile', name: 'mobile'},
                    {data: 'address', name: 'address'},
                    {data: 'due_amount', name: 'due_amount'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection
