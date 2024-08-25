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
                @if ($company->status == 'pending')
                <form action="{{ route('admin.company.change.status',$company->id) }}" method="POST" title="Accept">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="approved">
                    <a  onclick="void(0)" class="btn btn-success float-right ml-1 updateStatus">Approved</a>
                </form>
                @endif
                @if ($company->status == 'pending')
                    <form action="{{ route('admin.company.change.status',$company->id) }}" method="POST" title="Reject">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <a  onclick="void(0)" class="btn btn-danger float-right updateStatus">Rejected</a>
                    </form>
                @endif
            </div>
            <!-- /.row -->

        </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection

@push('script')
<script>
    $(window).on('load', function() {
        confirmAlert('.updateStatus', "Update Status", 'Yes, Confirm')
    });
</script>
@endpush
