@extends('layouts.app')
@section('title')
Purchase List
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2" style="background: #343A40; padding:8px; border-radius:6px; color:white">
            <div class="col-sm-6">
                <h1>Pre-define Recipe Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Pre-define Recipe Details</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Pre-define Recipe Details</h3>
                        {{-- <a href="{{ route('fg-purchase.pdf-download', encrypt($model->id)) }}"
                            class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i> PDF</a> --}}
                    </div>
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> Cake Town.
                                <small class="float-right">Date:{{ $model[0]->created_at }}</small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <address class="pl-3">
                                Address : {{ getSettingValue('company_address') }} <br>
                                Phone :  {{ getSettingValue('company_phone') }}<br>
                                Email : {{ getSettingValue('company_email') }}
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">

                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            Recipe No : {{ $model[0]->uid }} <br>
                            Name : {{ $model[0]->item->name }} <br>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>RM Group</th>
                                        <th>RM Product</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->coi->parent->name?? '' }}</td>
                                        <td>{{ $item->coi->name?? '' }}</td>
                                        <td>{{ $item->coi->unit->name?? '' }}</td>
                                        <td>{{ $item->qty?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- this row will not appear when printing -->
                </div>
                <!-- /.invoice -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->
@endsection
