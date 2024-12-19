@extends('layouts.app')
@section('title')
FG Requisition Details
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    @php    
        $links = [
        'Home'=>route('dashboard'),
        'FG Requisition Details'=>''
        ]
    @endphp
<x-breadcrumb title='FG Requisition Delivery Details' :links="$links"/>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">FG Requisition Delivery Details</h3>
                        <a href="{{route('fg-requisition-delivery.pdf',encrypt($fgRequisitionDelivery->id))}}"
                            class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i> PDF</a>
                    </div>
                    <!-- Main content -->
                    <div class="invoice p-3 mb-3">
                        <div class="row invoice-info">
                            <div class="col-sm-6 invoice-col">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left; padding:8px; line-height: 0.6">
                                                <p><b>FGR No :</b> {{ $fgRequisitionDelivery->requisition ? $fgRequisitionDelivery->requisition->uid : 'Not Available' }}</p>
                                                <p><b>Date :</b> {{ $fgRequisitionDelivery->date }} </p>
                                                <p><b>Delivered By :</b> {{ showUserInfo($fgRequisitionDelivery->createdBy) }} </p>
                                                <p><b>Status :</b> {!! showStatus($fgRequisitionDelivery->status) !!}</p>
                                            </td> 
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-6 invoice-col">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left; padding:8px; line-height: 0.6">
                                                <p><b>Outlet :</b> {{ $fgRequisitionDelivery->requisition->outlet ? $fgRequisitionDelivery->requisition->outlet->name : ''}}</p>
                                                <p><b>Store :</b> {{ $fgRequisitionDelivery->toStore->name ?? ''}}</p>
                                                <p><b>Address :</b> {{ $fgRequisitionDelivery->requisition->outlet ? $fgRequisitionDelivery->requisition->outlet->address : '' }}</p>
                                            </td> 
                                        </tr>
                                    </tbody>
                                </table>
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
                                            <th>Requsition QTY</th>
                                            <th>Delivery QTY</th>
                                            <th>Remaining QTY</th>
                                            {{-- <th>Rate</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fgRequisitionDelivery->items as $item)
                                        @php
                                            $requisition_qty = getRequisitionQty($item->requisition_id, $item->coi_id);
                                            $delivery_qty = $item->quantity;
                                        @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->coi->parent->name ?? '' }}</td>
                                                <td>{{ $item->coi->name ?? '' }}</td>
                                                <td>{{ $item->coi->unit->name ?? '' }}</td>
                                                <td>{{ $requisition_qty }}</td>
                                                <td>{{ $delivery_qty }}</td>
                                                <td>{{ max(($requisition_qty - $delivery_qty),0)  }}</td>
                                                {{-- <td>{{ $item->rate ?? '' }} TK</td> --}}
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
                            <!-- /.col -->
                            <div class="col-4">
                                {{-- <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">Subtotal:</th>
                                            <td>{{ $fgRequisitionDelivery->subtotal }} TK</td>
                                        </tr>
                                    </table>
                                </div> --}}
                            </div>
                            <!-- /.col -->
                        </div>
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
