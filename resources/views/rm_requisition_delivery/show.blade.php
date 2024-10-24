@extends('layouts.app')
@section('title')
RM Requisition Delivery Details
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    @php    
        $links = [
        'Home'=>route('dashboard'),
        'RM Requisition Delivery Details'=>''
        ]
    @endphp
<x-breadcrumb title='RM Requisition Delivery Details' :links="$links"/>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">RM Requisition Delivery Details</h3>
                        <a href="{{route('rm-requisition-delivery.pdf',encrypt($requisitionDelivery->id))}}"
                            class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i> PDF</a>
                    </div>
                    <!-- Main content -->
                    <div class="invoice p-3 mb-3">
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left; padding:8px; line-height: 0.6">
                                                <p><b>Date :</b> {{ $requisitionDelivery->date }} </p>
                                                <p><b>Transfer From :</b> {{ $requisitionDelivery->fromStore->name }} </p>
                                                <p><b>Transfer To :</b> {{ $requisitionDelivery->toStore->name }} </p>
                                                <p><b>Status :</b> {!! showStatus($requisitionDelivery->status) !!}</p>
                                            </td> 
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            
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
                                            <th>Item</th>
                                            <th>Unit</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($requisitionDelivery->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '' }}</td>
                                            <td>{{ $item->coi->name ?? '' }}</td>
                                            <td>{{ $item->coi->unit->name ?? '' }}</td>
                                            <td>{{ $item->quantity ?? '' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                        <!-- /.row -->
                    </div>
                </div>
                <!-- /.invoice -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->
@endsection
