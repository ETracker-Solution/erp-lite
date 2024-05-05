@extends('admin.layouts.master')
@section('title')
Product List
@endsection
@section('style')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
@endsection
@section('content')
<!-- Content Header (Page header) -->

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-1">

        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title" style="color:#115548;">Product List</h3>
                        <div class="card-tools">
                            <a href="{{route('product.index')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-back" aria-hidden="true"></i> &nbsp;Back</button></a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Category</th>
                                    <td class="text-center">{{$model->category->name}}</td>
                                    <th class="text-center">Name</th>
                                    <td class="text-center">{{$model->name}}</td>

                                </tr>
                                <tr>
                                    <th class="text-center">Code</th>
                                    <td class="text-center">{{$model->code}}</td>
                                    <!-- <th class="text-center">Image</th>
                                    <td class="text-center"><img width="auto" height="50" src="{{asset('storage/product/'.$model->img_url)}}" alt="{{ $model->code}}"></td> -->
                                </tr>
                                <tr>
                                    <th class="text-center">Sale Price</th>
                                    <td class="text-center">{{$model->selling_price}}</td>
                                    <th class="text-center">Stock</th>
                                    <td class="text-center">{{ \App\Classes\AvailableProductCalculation::product_id($model->id)}}</td>
                                </tr>
                                <tr>
                                    <th class="text-center">Description</th>
                                    <td class="text-center" colspan="3">{!! $model->description !!}</td>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
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
@section('script')
<!-- DataTables -->
<script src="{{ asset('admin/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ asset('admin/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
@endsection
@push('script-bottom')
<!-- page script -->
<script>
    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "autoWidth": false,
        });
        $('#example2').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
@endpush