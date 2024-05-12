@extends('layouts.app')
@section('title')
Employee
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
'Employee Details'=>''
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

                        <h3 class="card-title">Employee Details</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Name : </th>
                                    <td>{{ $employee->name }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile : </th>
                                    <td>{{ $employee->mobile }}</td>
                                </tr>
                                <tr>
                                    <th>Address : </th>
                                    <td>{{ $employee->address }}</td>
                                </tr>
                                <tr>
                                    <th>Email : </th>
                                    <td>{{ $employee->email }}</td>
                                </tr>
                                <tr>
                                    <th>Status : </th>
                                    <td>{!! showStatus($employee->status) !!}</td>
                                </tr>
                                <tr>
                                    <th>Type : </th>
                                    <td>{{ $employee->type }}</td>
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
