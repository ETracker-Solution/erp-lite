@extends('layouts.app')
@section('title')
Unit
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
                <!-- Horizontal Form -->
                <div class="card card-primary">
                    <div class="card-header bg-light">

                        <h3 class="card-title" style="color:#115548;">Add Unit</h3>
                        <div class="card-tools">
                            <a href="{{route('units.index')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i> &nbsp;See List</button></a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="POST" action="{{ route('units.update',$unit->id)}}" class="form form-horizontal" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        <div class="card-body">
                            <div class="row">
                                {{-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="unit_no">Unit No</label>
                                        <input type="text" class="form-control" name="unit_no" id="unit_no" value="{{$unit->unit_no}}">
                                    </div>
                                </div> --}}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{$unit->name}}">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <label for="short_name">Short Name</label>
                                        <input type="text" class="form-control" name="short_name" id="short_name" value="{{$unit->short_name}}">
                                    </div>
                                </div>
                                
                            </div>
                            <button class="btn btn-primary waves-effect waves-float waves-light float-right"
                                        type="submit">Submit
                            </button>
                        </div>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-2"></div>
        </div>
        <!-- /.row -->

    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection