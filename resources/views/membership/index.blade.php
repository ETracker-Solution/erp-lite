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
                <div class="row align-items-end mb-3">
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="date_range">Date Range</label>
                            <input type="text" id="date_range" class="form-control filter-input flatpickr-range" placeholder="YYYY-MM-DD to YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label for="customer_name">Customer</label>
                            <input type="text" id="customer_name" class="form-control filter-input" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" class="form-control filter-input" placeholder="Phone">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label for="member_type">Member Type</label>
                            <select id="member_type" class="form-control filter-input select2">
                                <option value="">All Types</option>
                                @foreach($memberTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                         <div class="form-group mb-0">
                            <button type="button" id="reset-btn" class="btn btn-secondary btn-block">Reset</button>
                        </div>
                    </div>
                </div>
                <hr>

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
<link rel="stylesheet" type="text/css" href="{{ asset('datepicker/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('datepicker/app-assets/css/plugins/forms/pickers/form-flat-pickr.css') }}">
@endsection
@push('style')

@endpush
@section('js')
<!-- DataTables -->
<script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{ asset('datepicker/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
@endsection
@push('script')
<script>
    function recallDatatable() {
        $('#dataTable').DataTable().draw(true);
    }

    $(document).ready(function () {
            if (sessionStorage.getItem('date_range_membership')) {
                $('#date_range').val(sessionStorage.getItem('date_range_membership'));
            }
            if (sessionStorage.getItem('customer_name_membership')) {
                $('#customer_name').val(sessionStorage.getItem('customer_name_membership'));
            }
            if (sessionStorage.getItem('phone_membership')) {
                $('#phone').val(sessionStorage.getItem('phone_membership'));
            }
            if (sessionStorage.getItem('member_type_membership')) {
                $('#member_type').val(sessionStorage.getItem('member_type_membership'));
            }

            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('memberships.index') }}",
                    data: function (d) {
                        d.date_range = $('#date_range').val();
                        d.customer_name = $('#customer_name').val();
                        d.phone = $('#phone').val();
                        d.member_type = $('#member_type').val();
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
                        data: "customer.name",
                        title: "Customer",
                        name: "customer.name",
                        searchable: true,
                        orderable: false,
                    },
                    {
                        data: "customer.mobile",
                        title: "Phone",
                        name: "customer.mobile",
                        searchable: true,
                        orderable: false,
                    },
                    {
                        data: "member_type.name",
                        title: "Member Type",
                        name: "memberType.name",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: "point",
                        title: "Point",
                        name: "point",
                        searchable: false,
                        orderable: false,
                    },

                    {
                        data: "created_at",
                        title: "created at",
                        name: "created_at",
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

            $('.flatpickr-range').flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
            });

            $('.filter-input').on('keyup change', function () {
                sessionStorage.setItem('date_range_membership', $('#date_range').val());
                sessionStorage.setItem('customer_name_membership', $('#customer_name').val());
                sessionStorage.setItem('phone_membership', $('#phone').val());
                sessionStorage.setItem('member_type_membership', $('#member_type').val());
                recallDatatable();
            });

            $('#reset-btn').on('click', function () {
                $('.filter-input').val('');
                if ($('#member_type').hasClass('select2')) {
                    $('#member_type').val('').trigger('change');
                }
                if ($('#date_range').length > 0 && $('#date_range')[0]._flatpickr) {
                    $('#date_range')[0]._flatpickr.clear();
                }
                sessionStorage.removeItem('date_range_membership');
                sessionStorage.removeItem('customer_name_membership');
                sessionStorage.removeItem('phone_membership');
                sessionStorage.removeItem('member_type_membership');
                recallDatatable();
            });
        })
</script>

@endpush