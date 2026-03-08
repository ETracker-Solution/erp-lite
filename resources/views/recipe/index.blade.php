@extends('layouts.app')
@section('title')
    Purchase List
@endsection
@section('content')
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Pre-define Recipe list'=>''
        ]
    @endphp
    <x-breadcrumb title='Pre-define Recipe' :links="$links"/>
    <!-- Main content -->
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
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label for="recipe_no">Recipe No</label>
                                <input type="text" id="recipe_no" class="form-control filter-input" placeholder="Recipe No">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label for="product">Product</label>
                                <input type="text" id="product" class="form-control filter-input" placeholder="Product Name">
                            </div>
                        </div>
                        <div class="col-md-2">
                             <div class="form-group mb-0">
                                <button type="button" id="reset-btn" class="btn btn-secondary btn-block">Reset</button>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title">Pre-define Recipe List</h3>
                            <div class="card-tools">
                                <a href="{{route('recipes.create')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"
                                                                              aria-hidden="true"></i> &nbsp;Add New
                                    </button>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="dataTable" class="table table-bordered">
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
    <!-- page script -->
    <script>
        function recallDatatable() {
            $('#dataTable').DataTable().draw(true);
        }

        $(document).ready(function () {
            if (sessionStorage.getItem('date_range_recipe')) {
                $('#date_range').val(sessionStorage.getItem('date_range_recipe'));
            }
            if (sessionStorage.getItem('recipe_no_recipe')) {
                $('#recipe_no').val(sessionStorage.getItem('recipe_no_recipe'));
            }
            if (sessionStorage.getItem('product_recipe')) {
                $('#product').val(sessionStorage.getItem('product_recipe'));
            }

            $('#dataTable').dataTable({
                stateSave: true,
                responsive: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('recipes.index') }}",
                    data: function (d) {
                        d.date_range = $('#date_range').val();
                        d.recipe_no = $('#recipe_no').val();
                        d.product = $('#product').val();
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
                        data: "uid",
                        title: "Recipe No",
                        name: "uid",
                        searchable: true
                    },
                    {
                        data: "item.name",
                        title: "Product",
                        name: "item.name",
                        searchable: true
                    },
                    {
                        data: "created_at",
                        title: "Date",
                        name: "created_at",
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

            $('.flatpickr-range').flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
            });

            $('.filter-input').on('keyup change', function () {
                sessionStorage.setItem('date_range_recipe', $('#date_range').val());
                sessionStorage.setItem('recipe_no_recipe', $('#recipe_no').val());
                sessionStorage.setItem('product_recipe', $('#product').val());
                recallDatatable();
            });

            $('#reset-btn').on('click', function () {
                $('.filter-input').val('');
                if ($('#date_range').length > 0 && $('#date_range')[0]._flatpickr) {
                    $('#date_range')[0]._flatpickr.clear();
                }
                sessionStorage.removeItem('date_range_recipe');
                sessionStorage.removeItem('recipe_no_recipe');
                sessionStorage.removeItem('product_recipe');
                recallDatatable();
            });
        })
    </script>
@endpush
