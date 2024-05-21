@extends('layouts.app')
@section('title')
FG Inventory Transfer Details
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>FG Inventory Transfer</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">FG Inventory Transfer</li>
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
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> Company Name.
                                <small class="float-right">Date:{{ $fGInventoryTransfer->created_at }}</small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <address>
                                Address : 17/1, 60 Feet, Mirpur, Dhaka-1215
                                <br>
                                Phone: +880 1710355789<br>
                                Email:info.company@gmail.com
                            </address>
                        </div>
                        <!-- /.col -->
                        {{-- <div class="col-sm-4 invoice-col">
                            <td>Customer:</td>
                        </div> --}}
                        <!-- /.col -->
                        {{-- <div class="col-sm-4 invoice-col">
                            <b>Invoice:</b>
                        </div> --}}
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
                                        <th>Group</th>
                                        <th>Name</th>
                                        <th>Unit</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->coi->parent ? $item->coi->parent->name : '' }}</td>
                                        <td>{{ $item->coi ? $item->coi->name : '' }}</td>
                                        <td>{{ $item->coi->unit->name }}</td>
                                        <td>{{ $item->rate ?? '' }}</td>
                                        <td>{{ $item->quantity ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-8">

                        </div>
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.invoice -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->
@endsection
