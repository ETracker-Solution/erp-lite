@extends('layouts.app')
@section('title')
    Sales delivery details
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Sales delivery details'=>''
        ]
    @endphp
    <x-breadcrumb title='Sales delivery details' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Sales delivery details</h3>
                            <a href="{{route('others.outlet.pdf-download',encrypt($otherOutletSale->id))}}"
                               class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i>
                                PDF</a>
                        </div>
                        <!-- Main content -->
                        {{-- @php
                            dd($otherOutletSale);
                        @endphp --}}
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                                <table width="100%">
                                    <tbody>
                                    <tr>
                                        <td style="text-align: left; padding:8px; line-height: 0.6">
                                            <p><b>Invoice No :</b> {{ $otherOutletSale->invoice_number }}</p>
                                            <p><b>Order From :</b> {{ $otherOutletSale->outlet->name }}</p>
                                            <p><b>Delivery Point :</b> {{ $otherOutletSale->deliveryPoint->name }}</p>
                                            <p><b>Date :</b> {{ $otherOutletSale->date }} </p>
                                            <p><b>Sub Total :</b> {{ $otherOutletSale->subtotal }} </p>
                                            <p><b> Delivery Status :</b> {!! showStatus($otherOutletSale->status) !!}
                                            <p><b> Seller Name :</b> {{ $otherOutletSale->createdBy->name }}
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
                                        <th>Unit</th>
                                        <th>Quantity</th>
                                        <th>Discount</th>
                                        <th class="text-right">Grand Total</th>

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
                                            <td>{{ $item->discount ? $item->discount : 0 }}</td>
                                            <td class="text-right">{{  $item->unit_price*$item->quantity }}</td>
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
                                            <td class="text-right">{{ $otherOutletSale->subtotal }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Delivery Charge:</th>
                                            <td class="text-right">{{ $otherOutletSale->delivery_charge }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Additional Charge:</th>
                                            <td class="text-right">{{ $otherOutletSale->additional_charge }}</td>
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
                                        @endphp
                                        <tr>
                                            <th style="width:50%">Paid</th>
                                            <td class="text-right">{{ $paid  }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Due</th>
                                            <td class="text-right">{{ max($otherOutletSale->grand_total - $paid,0)}}</td>
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
