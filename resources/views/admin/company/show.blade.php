@extends('admin.layouts.app')
@section('title')
Company
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
'Home'=>route('admin.admin_dashboard'),
'Company Details'=>''
]
@endphp
<x-breadcrumb title='Company' :links="$links" />



<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Horizontal Form -->
                <div class="card card-info">
                    <div class="card-header">

                        <h3 class="card-title">Company Details</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body p-0">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Name : </th>
                                    <td>{{ $company->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email : </th>
                                    <td>{{ $company->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone : </th>
                                    <td>{{ $company->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Address : </th>
                                    <td>{{ $company->address }}</td>
                                </tr>
                                <tr>
                                    <th>Number of Employee : </th>
                                    <td>{{ $company->no_of_employee }}</td>
                                </tr>
                                <tr>
                                    <th>Type : </th>
                                    <td>{{ $company->type }}</td>
                                </tr>
                                <tr>
                                    <th>Status : </th>
                                    <td>{!! showStatus($company->status) !!}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <form action="{{ route('admin.company.change.status',$company->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    {{-- @if ($company->status == 'pending')
                        <a title="Active"><button class="btn btn-success float-right">Approved</button></a>
                        @else
                        <a title="Pending"><button class="btn btn-danger float-right">Pending</button></i></a>
                    @endif --}}
                    @if ($company->status == 'pending')
                        <a title="Active"><button class="btn btn-success float-right">Active</button></a>
                        @elseif($company->status == 'active')
                        <a title="Pending"><button class="btn btn-danger float-right">Inactive</button></i></a>
                        @elseif($company->status == 'inactive')
                        <a title="Active"><button class="btn btn-success float-right">Active</button></i></a>
                    @endif
                </form>
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection

@push('script')

@endpush
