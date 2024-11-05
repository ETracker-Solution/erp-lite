@extends('layouts.app')
@section('title')
FG Inventory Transfer Receive Details
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'FG Inventory Transfer Receive Details'=>''
        ]
    @endphp
<x-breadcrumb title='FG Inventory Transfer Receive Details' :links="$links"/>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">FG Inventory Transfer Receive Details</h3>
                        <a href="{{route('fg-transfer-receive.pdf',encrypt($fgTransferReceive->id))}}"
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
                                                <p><b>FGITR No :</b> {{ $fgTransferReceive->uid }}</p>
                                                <p><b>Date :</b> {{ $fgTransferReceive->date }} </p>
                                                <p><b>Status :</b> {!! showStatus($fgTransferReceive->status) !!}</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">
                                {{-- <td><b>Customer :</b> {{ $fgTransferReceive->customer->name??'Walking Customer'}}</td> --}}
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
                                            <th>Date</th>
                                            <th>From Store</th>
                                            <th>To Store</th>
                                            <th>Group</th>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th>Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                    $totalQty = 0;
                                    $totalValue = 0;
                                    @endphp
                                        @foreach ($fgTransferReceive->items as $item)
                                            @php
                                                $totalQty += $item->quantity;
                                                $totalValue += ($item->quantity * $item->rate);
                                            @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $fgTransferReceive->date }}</td>
                                            <td>{{ $fgTransferReceive->fromStore->name ?? '' }}</td>
                                            <td>{{ $fgTransferReceive->toStore->name ?? '' }}</td>
                                            <td>{{ $item->coi->parent->name ?? '' }}</td>
                                            <td>{{ $item->coi->name ?? '' }}</td>
                                            <td>{{ $item->quantity ?? '' }}</td>
                                            <td>{{ $item->rate ?? '' }}</td>
                                            <td>{{ $item->quantity * $item->rate }} TK</td>
                                        </tr>
                                        @endforeach
                                    <tr>

                                        <td colspan="6">Total</td>
                                        <td>{{ $totalQty }}</td>
                                        <td></td>
                                        <td>{{ $totalValue }}</td>
                                    </tr>
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
