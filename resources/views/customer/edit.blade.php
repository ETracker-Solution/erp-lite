@extends('layouts.app')
@section('title')
Customer
@endsection
@section('style')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('admin/plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection
@section('content')
@php
$links = [
'Home'=>route('dashboard'),
'Master Data'=>'',
'Customer Edit'=>''
]
@endphp
<x-breadcrumb title='Customer' :links="$links" />



<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Horizontal Form -->
                <div class="card card-info">
                    <div class="card-header">

                        <h3 class="card-title">Edit Customer</h3>
                        <div class="card-tools">
                            <a href="{{route('customers.index')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i> &nbsp;See List</button></a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="POST" action="{{ route('customers.update',$customer->id)}}" class="form form-horizontal" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.text label="Name" inputName="name" placeholder="Enter Name" :isRequired='true' :isReadonly='false' :defaultValue="$customer->name" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.text label="Mobile" inputName="mobile" placeholder="Enter Mobile" :isRequired='true' :isReadonly='false' :defaultValue="$customer->mobile" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.text label="Address" inputName="address" placeholder="Enter Address" :isRequired='true' :isReadonly='false' :defaultValue="$customer->address" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.email label="Email" inputName="email" placeholder="Enter Email" :isRequired='true' :isReadonly='false' :defaultValue="$customer->email" />
                                </div>
                            </div>
                            <button class="btn btn-info waves-effect waves-float waves-light float-right" type="submit">Update
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
@push('script')

@endpush
