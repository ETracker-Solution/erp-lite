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
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="name">Supplier Name</label>
                                    <input type="text" id="name" class="form-control filter-input" placeholder="Supplier Name">
                                </div>
                                <div class="col-md-3">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" id="mobile" class="form-control filter-input" placeholder="Mobile">
                                </div>
                                <div class="col-md-4">
                                    <label for="address">Address</label>
                                    <input type="text" id="address" class="form-control filter-input" placeholder="Address">
                                </div>
                                <div class="col-md-2">
                                    <button id="reset_filter" class="btn btn-warning btn-block">Reset</button>
                                </div>
                            </div>
                            <div class="table-responsive">
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
        </div>
    </section>
@endsection

@section('js')
    <script>
        $(function () {
            var table = $('#supplierDueTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('supplier-dues.index') }}",
                    data: function (d) {
                        d.name = $('#name').val();
                        d.mobile = $('#mobile').val();
                        d.address = $('#address').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'mobile', name: 'mobile'},
                    {data: 'address', name: 'address'},
                    {data: 'due_amount', name: 'due_amount', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            $('.filter-input').on('change keyup', function () {
                table.draw();
            });

            $('#reset_filter').click(function () {
                $('.filter-input').val('');
                table.draw();
            });
        });
    </script>
@endsection
