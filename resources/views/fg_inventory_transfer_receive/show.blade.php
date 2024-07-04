@extends('layouts.app')
@section('title')
FG Delivery Receive Details
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    @php    
        $links = [
        'Home'=>route('dashboard'),
        'FG Delivery Receive Details'=>''
        ]
    @endphp
<x-breadcrumb title='FG Delivery Receive Details' :links="$links"/>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">FG Delivery Receive Details</h3>
                        <a href="{{route('fg-delivery-receive.pdf',encrypt($fgDeliveryReceive->id))}}"
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
                                                <p><b>FGR No :</b> {{ $fgDeliveryReceive->requisition ? $fgDeliveryReceive->requisition->uid : 'Not Available' }}</p>
                                                <p><b>Date :</b> {{ $fgDeliveryReceive->date }} </p>
                                                <p><b>Sub Total :</b> {{ $fgDeliveryReceive->subtotal }} </p>
                                                <p><b>Status :</b> {!! showStatus($fgDeliveryReceive->status) !!}</p>
                                            </td> 
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">
                                <td><b>Customer :</b> {{ $fgDeliveryReceive->customer->name??'Walking Customer'}}</td>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">
                                 
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
                                            <th>Group</th>
                                            <th>Item</th>
                                            <th>Unit</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fgDeliveryReceive->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '' }}</td>
                                            <td>{{ $item->coi->name ?? '' }}</td>
                                            <td>{{ $item->coi->unit->name ?? '' }}</td>
                                            <td>{{ $item->quantity ?? '' }}</td>
                                            <td>{{ $item->rate ?? '' }} TK</td>
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