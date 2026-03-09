@extends('layouts.app')
@section('title', 'Customer Due List')
@section('content')
    @php
        $links = [
            'Home'=>route('dashboard'),
            'Accounts Module'=>'',
            'Customer Due List'=>'',
        ]
    @endphp
    <x-breadcrumb title='Customer Due List' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="row align-items-end mb-3">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label for="customer_name">Customer Name</label>
                                <input type="text" id="customer_name" class="form-control filter-input" placeholder="Search by Name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" class="form-control filter-input" placeholder="Search by Phone">
                            </div>
                        </div>
                        <div class="col-md-2">
                             <div class="form-group mb-0">
                                <button type="button" id="reset-btn" class="btn btn-secondary btn-block">Reset</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                             <div class="form-group mb-0">
                                <button type="button" id="export-btn" class="btn btn-success btn-block"><i class="fas fa-file-excel"></i> Export</button>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Customer Due List</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped dt-responsive nowrap dataTable no-footer dtr-inline table-sm"
                                   id="customerDueTable">
                                <thead>
                                <tr>
                                    <th>Customer Name</th>
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
        function recallDatatable() {
            $('#customerDueTable').DataTable().draw(true);
        }

        $(function () {
            if (sessionStorage.getItem('customer_name_due')) {
                $('#customer_name').val(sessionStorage.getItem('customer_name_due'));
            }
            if (sessionStorage.getItem('phone_due')) {
                $('#phone').val(sessionStorage.getItem('phone_due'));
            }

            $('#customerDueTable').DataTable({
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: {
                    url: "{{ route('customer-dues.index') }}",
                    data: function (d) {
                        d.customer_name = $('#customer_name').val();
                        d.phone = $('#phone').val();
                    }
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'mobile', name: 'mobile'},
                    {data: 'address', name: 'address'},
                    {data: 'due_amount', name: 'due_amount'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            $('.filter-input').on('keyup change', function () {
                sessionStorage.setItem('customer_name_due', $('#customer_name').val());
                sessionStorage.setItem('phone_due', $('#phone').val());
                recallDatatable();
            });

            $('#reset-btn').on('click', function () {
                $('.filter-input').val('');
                sessionStorage.removeItem('customer_name_due');
                sessionStorage.removeItem('phone_due');
                recallDatatable();
            });

            $('#export-btn').on('click', function () {
                let customer_name = $('#customer_name').val();
                let phone = $('#phone').val();
                let url = "{{ route('customer-dues.export') }}?" + $.param({
                    customer_name: customer_name,
                    phone: phone
                });
                window.location.href = url;
            });
        });
    </script>
@endsection

