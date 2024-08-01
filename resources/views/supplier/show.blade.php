@extends('layouts.app')
@section('title')
Supplier
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
'Purchase Setting'=>'',
'Supplier Details'=>''
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

                        <h3 class="card-title">Supplier Details</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Group : </th>
                                    <td>{{ $supplier->group ? $supplier->group->name : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Name : </th>
                                    <td>{{ $supplier->name }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile : </th>
                                    <td>{{ $supplier->mobile }}</td>
                                </tr>
                                <tr>
                                    <th>Address : </th>
                                    <td>{{ $supplier->address }}</td>
                                </tr>
                                <tr>
                                    <th>Email : </th>
                                    <td>{{ $supplier->email }}</td>
                                </tr>
                                <tr>
                                    <th>Status : </th>
                                    <td>{!! showStatus($supplier->status) !!}</td>
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
