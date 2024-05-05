@extends('layouts.app')
@section('title')
Purchase List
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Invoice</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Invoice</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info"></i> Note:</h5>
                    This page has been enhanced for printing. Click the print button at the bottom of the invoice to
                    test.
                </div>


                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> E-Moto Bazar.
                                <small class="float-right">Date:{{ $model->created_at }}</small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <address>
                            Address : 17/1, Monipuripara, Sangshad Avenue,
                            <br>Dhaka-1215 Bangladesh<br>
                                Phone:  +880 1977-722531<br>
                                Email:info.emotobazar@gmail.com
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">

                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <b>Invoice: {{ $model->invoice_number }}</b>
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
                                        <th>Product</th>
                                        <th>Serial #</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Item Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model->items as $item)


                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product->name?? '' }}</td>
                                        <td>{{ $item->product->code?? '' }}</td>
                                        <td>{{ $item->quantity?? '' }} {{ $item->product->unit_of_measurement->name?? '' }}</td>
                                        <td>{{ $item->buying_price?? '' }} TK</td>
                                        <td>{{ $item->buying_price * $item->quantity?? '' }} TK</td>
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
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <th style="width:50%">Subtotal:</th>
                                        <td>{{ $model->grand_total }} TK</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- this row will not appear when printing -->
                    <div class="row no-print">
                        <div class="col-12">
                            <a href="{{ route('purchase.pdf', $model->id) }}" target="_blank" class="btn btn-default float-right">
                                <i class="fas fa-print"></i> Print</a>
                            <a href="{{ route('purchase.pdf-download', $model->id) }}" class="btn btn-primary float-right">
                                <i class="fas fa-download"></i> Generate PDF</a>

                        </div>
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
