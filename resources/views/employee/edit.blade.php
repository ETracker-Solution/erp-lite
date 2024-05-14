@extends('layouts.app')
@section('title')
Employee
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
'Employee Edit'=>''
]
@endphp
<x-breadcrumb title='Employee' :links="$links" />



<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Horizontal Form -->
                <div class="card card-info">
                    <div class="card-header">

                        <h3 class="card-title">Edit Employee</h3>
                        <div class="card-tools">
                            <a href="{{route('employees.index')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i> &nbsp;See List</button></a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="POST" action="{{ route('employees.update',$employee->id)}}" class="form form-horizontal" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.text label="Name" inputName="name" placeholder="Enter Name" :isRequired='true' :isReadonly='false' :defaultValue="$employee->name" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.email label="Email" inputName="email" placeholder="Enter Email" :isRequired='true' :isReadonly='false' :defaultValue="$employee->email" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.text label="Phone" inputName="phone" placeholder="Enter Phone" :isRequired='true' :isReadonly='false' :defaultValue="$employee->phone" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.text label="Address" inputName="address" placeholder="Enter Address" :isRequired='true' :isReadonly='false' :defaultValue="$employee->address" />
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
