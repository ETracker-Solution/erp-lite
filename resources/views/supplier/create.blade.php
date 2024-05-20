@extends('layouts.app')
@section('title')
    Supplier Entry
@endsection
@section('style')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection
@section('content')
@php
$links = [
'Home'=>route('dashboard'),
'Supplier Entry'=>''
]
@endphp
<x-breadcrumb title='Supplier' :links="$links"/>



    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="{{route('suppliers.store')}}" method="POST" class=""
                          enctype="multipart/form-data">
                        @csrf
                        <!-- Horizontal Form -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Supplier Entry</h3>
                                <div class="card-tools">
                                    <a href="{{route('suppliers.index')}}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-list"
                                           aria-hidden="true"></i>
                                        &nbsp;See List

                                    </a>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->


                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                   placeholder="Enter Name"
                                                   value="{{old('name')}}">
                                            @if($errors->has('name'))
                                                <small class="text-danger">{{$errors->first('name')}}</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="mobile">Mobile</label>
                                            <input type="text" class="form-control" id="mobile" name="mobile"
                                                   placeholder="Enter Mobile"
                                                   value="{{old('mobile')}}">
                                            @if($errors->has('mobile'))
                                                <small class="text-danger">{{$errors->first('mobile')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="text" class="form-control" id="email" name="email"
                                                   placeholder="Enter Email Address"
                                                   value="{{old('email')}}">
                                            @if($errors->has('email'))
                                                <small class="text-danger">{{$errors->first('email')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" id="address" name="address"
                                                      placeholder="Enter Email Address">{{old('address')}}</textarea>
                                            @if($errors->has('address'))
                                                <small class="text-danger">{{$errors->first('address')}}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-4 col-12 mb-1">
                                        <x-forms.select label="Suppllier Group" inputName="supplier_group_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' defaultValue="" :options="$supplier_groups" optionId="id" optionValue="name"/>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button class="btn btn-info float-right"><i class="fa fa-check" aria-hidden="true"></i>
                                    Submit
                                </button>
                            </div>
                        </div>
                        <!-- /.card -->
                    </form>
                </div>
                <div class="col-2"></div>
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection

@push('js')
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js')}}"></script>
    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

        })
    </script>

@endpush
