@extends('layouts.app')
@section('title')
    Other Outlet Sales Details
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Other Outlet Sales Details'=>''
        ]
    @endphp
    <x-breadcrumb title='Other Outlet Sales Details' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Other Outlet Sales Details</h3>
                            <a href="{{route('others.outlet.pdf-download',encrypt($otherOutletSale->id))}}"
                               class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i>
                                PDF</a>
                        </div>
                        <!-- Main content -->
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                                <table width="100%">
                                    <tbody>
                                    <tr>
                                        <td style="text-align: left; padding:8px; line-height: 0.6">
                                            <p><b>Invoice No :</b> {{ $otherOutletSale->invoice_number }}</p>
                                            <p><b>Delivery Point :</b> {{ $otherOutletSale->deliveryPoint->name }}</p>
                                            <p><b>Date :</b> {{ $otherOutletSale->date }} </p>
                                            <p><b>Sub Total :</b> {{ $otherOutletSale->subtotal }} </p>
                                            <p><b>Delivery Status :</b> {!! showStatus($otherOutletSale->status) !!}
                                            </p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">
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
                                        <th>Invoice No</th>
                                        <th>Item</th>
                                        <th>Unit Price</th>
                                        <th>Quantity</th>
                                        <th>Discount</th>
                                        <th class="text-right">item Total</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($otherOutletSale->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $otherOutletSale->invoice_number }}</td>
                                            <td>{{ $item->coi->name ?? '' }}</td>
                                            <td>{{ $item->unit_price ?? '' }}</td>
                                            <td>{{ $item->quantity ?? '' }}</td>
                                            <td>{{ $item->discount }}</td>
                                            <td class="text-right">{{$item->unit_price*$item->discount }}</td>
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
                                <p class="lead">Payment Methods:</p>
                                <table class="table table-sm table-bordered" style="width: 50%">
                                    <thead>
                                        <tr>
                                            <th>Method (Phase)</th>
                                            <th class="text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $initial_payments = json_decode($otherOutletSale->payment_methods, true) ?? [];
                                            $sale = \App\Models\Sale::where('invoice_number', $otherOutletSale->invoice_number)->first();
                                            $delivery_payments = $sale ? $sale->payments : collect();
                                        @endphp
                                        @foreach($initial_payments as $payment)
                                            <tr>
                                                <td>{{ ucfirst($payment['method'] ?? 'N/A') }} (Order)</td>
                                                <td class="text-right">{{ number_format($payment['amount'] ?? 0, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        @foreach($delivery_payments as $payment)
                                            <tr>
                                                <td>{{ ucfirst($payment->payment_method) }} (Delivery)</td>
                                                <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        @if(empty($initial_payments) && $delivery_payments->isEmpty())
                                            <tr>
                                                <td colspan="2">No payment info found</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-4">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">Subtotal:</th>
                                            <td class="text-right">{{ $otherOutletSale->subtotal }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Discount:</th>
                                            <td class="text-right">{{ $otherOutletSale->discount }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Grand Total</th>
                                            <td class="text-right">{{ $otherOutletSale->grand_total }}</td>
                                        </tr>
                                        @php
                                            $paid = $otherOutletSale->receive_amount +  $otherOutletSale->delivery_point_receive_amount;
//                                            $paid = ($delivery_payments->sum('amount') > 0) ? $delivery_payments->sum('amount') + $otherOutletSale->receive_amount : $otherOutletSale->receive_amount + $otherOutletSale->delivery_point_receive_amount;
                                        @endphp
                                        <tr>
                                            <th style="width:50%">Paid</th>
                                            <td class="text-right">{{ $paid  }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Due</th>
                                            <td class="text-right">{{ max($otherOutletSale->grand_total - $paid, 0) }}</td>
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
