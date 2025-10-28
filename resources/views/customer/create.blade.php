@extends('layouts.app')
@section('title')
Customer Entry
@endsection
@section('style')
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection
@section('content')
<!-- Content Header (Page header) -->
@php
    $links = [
    'Home'=>route('dashboard'),
    'Master Data'=>'',
    'Customer Entry'=>''
    ]
@endphp
<x-breadcrumb title='Customer' :links="$links"/>



<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Horizontal Form -->
                <div class="card card-info">
                    <div class="card-header">

                        <h3 class="card-title">Customer Entry</h3>
                        <div class="card-tools">
                            <a href="{{route('customers.index')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i> &nbsp;See List</button></a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <form action="{{ route('customers.store') }}" method="POST" class="">
                        @csrf

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                              <x-forms.text label="Name" inputName="name" placeholder="Enter Name"
                                  :isRequired='true' :isReadonly='false' defaultValue="" />
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                              <x-forms.text label="Mobile" inputName="mobile" placeholder="Enter Mobile"
                                  :isRequired='true' :isReadonly='false' defaultValue="" />
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                              <x-forms.text label="Address" inputName="address" placeholder="Enter Address"
                                  :isRequired='false' :isReadonly='false' defaultValue="" />
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                              <x-forms.email label="Email" inputName="email" placeholder="Enter Email"
                                  :isRequired='false' :isReadonly='false' defaultValue="" />
                            </div>
                        </div>
                        <button class="btn btn-info waves-effect waves-float waves-light float-right"
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

@push('script')

@endpush
