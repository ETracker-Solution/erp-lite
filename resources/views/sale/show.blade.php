@extends('layouts.app')
@section('title')
Sales Details
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    @php    
        $links = [
        'Home'=>route('dashboard'),
        'Sales Details'=>''
        ]
    @endphp
<x-breadcrumb title='Sales Details' :links="$links"/>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Sales Details</h3>
                        <a href="{{route('sale.pdf-download',encrypt($sale->id))}}"
                            class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i> PDF</a>
                    </div>
                    <!-- Main content -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td style="text-align: left; padding:8px; line-height: 0.6">
                                            <p><b>Invoice No :</b> {{ $sale->invoice_number }}</p>
                                            <p><b>Date :</b> {{ $sale->date }} </p>
                                            <p><b>Sub Total :</b> {{ $sale->subtotal }} </p>
                                            <p><b>Status :</b> {!! showStatus($sale->status) !!}</p>
                                        </td> 
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td style="text-align: left; padding:8px; line-height: 0.6">
                                            <p><b>Outlet :</b> {{ $sale->outlet->name }}</p>
                                            <p><b>Address :</b> {{ $sale->outlet->address }}</p>
                                        </td> 
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td style="text-align: left; padding:8px; line-height: 0.6">
                                            <p><b>Customer :</b> {{ $sale->customer->name }}</p>
                                            <p><b>email :</b> {{ $sale->customer->email }} </p>
                                            <p><b>Phone :</b> {{ $sale->customer->mobile }} </p>
                                            <p><b>Address :</b> {{ $sale->customer->address }}</p>
                                        </td> 
                                    </tr>
                                </tbody>
                            </table>
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
                                        <th>Invoice No</th>
                                        <th>Item</th>
                                        <th>Unit</th>
                                        <th>Quantity</th>
                                        <th>Discount</th>
                                        <th class="text-right">Grand Total</th>    

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sale->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sale->invoice_number }}</td>
                                            <td>{{ $item->coi->name ?? '' }}</td>
                                            <td>{{ $item->unit_price ?? '' }}</td>
                                            <td>{{ $item->quantity ?? '' }}</td>
                                            <td>{{ $sale->discount }}</td>
                                            <td class="text-right">{{ $sale->grand_total }}</td>    
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-8">

                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Subtotal:</th>
                                        <td class="text-right">{{ $sale->subtotal }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width:50%">Discount:</th>
                                        <td class="text-right">{{ $sale->discount }}</td>
                                    </tr>
                                    <tr>
                                        <th style="width:50%">Grand Total</th>
                                        <td class="text-right">{{ $sale->grand_total }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.row -->
            </div>
                <!-- /.invoice -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->
@endsection
