@extends('layouts.app')
@section('title')
Customer
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
'Customer Details'=>''
]
@endphp
<x-breadcrumb title='Customer Details' :links="$links" />



<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Horizontal Form -->
                <div class="card card-info">
                    <div class="card-header">

                        <h3 class="card-title">Customer Details</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Name : </th>
                                    <td>{{ $customer->name }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile : </th>
                                    <td>{{ $customer->mobile }}</td>
                                </tr>
                                <tr>
                                    <th>Address : </th>
                                    <td>{{ $customer->address }}</td>
                                </tr>
                                <tr>
                                    <th>Email : </th>
                                    <td>{{ $customer->email }}</td>
                                </tr>
                                <tr>
                                    <th>Status : </th>
                                    <td>{{ $customer->status }}</td>
                                </tr>
                                <tr>
                                    <th>Type : </th>
                                    <td>{{ $customer->type }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection

@push('script')

@endpush
