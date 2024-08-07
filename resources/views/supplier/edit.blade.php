@extends('layouts.app')
@section('title')
Supplier Edit
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
'Purchase Setting'=>'',
'Supplier Edit'=>''
]
@endphp
<x-breadcrumb title='Supplier' :links="$links" />



<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Horizontal Form -->
                <div class="card card-info">
                    <div class="card-header">

                        <h3 class="card-title">Supplier Edit</h3>
                        <div class="card-tools">
                            <a href="{{route('suppliers.index')}}"><button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i> &nbsp;See List</button></a>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form method="POST" action="{{ route('suppliers.update',$supplier->id)}}" class="form form-horizontal" enctype="multipart/form-data">
                        @csrf
                        @method('put')

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.text label="Name" inputName="name" placeholder="Enter Name" :isRequired='true' :isReadonly='false' :defaultValue="$supplier->name" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.text label="Mobile" inputName="mobile" placeholder="Enter Mobile" :isRequired='true' :isReadonly='false' :defaultValue="$supplier->mobile" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.text label="Address" inputName="address" placeholder="Enter Address" :isRequired='true' :isReadonly='false' :defaultValue="$supplier->address" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                    <x-forms.email label="Email" inputName="email" placeholder="Enter Email" :isRequired='true' :isReadonly='false' :defaultValue="$supplier->email" />
                                </div>
                                <div class="col-xl-4 col-md-4 col-12 mb-1">
                                  <x-forms.select label="Suppllier Group" inputName="supplier_group_id" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="$supplier->supplier_group_id" :options="$supplier_groups" optionId="id" optionValue="name"/>
                                </div>
                                <div class="col-xl-4 col-md-4 col-12 mb-1">
                                    <x-forms.static-select label="Status" inputName="status" placeholder="Select One" :isRequired='true'  :isReadonly='false' :defaultValue="$supplier ? $supplier->status : ''" :options="['active','inactive']"/>
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
