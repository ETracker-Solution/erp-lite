@extends('layouts.app')
@section('title')
RM Inventory Adjustment Details
@endsection
@section('content')
    @php    
        $links = [
        'Home'=>route('dashboard'),
        'RM Inventory Adjustment'=>''
        ]
    @endphp
<x-breadcrumb title='RM Inventory Adjustment Details' :links="$links"/>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <!-- info row -->
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td style="text-align: left; padding:8px; line-height: 0.6">
                                            <p><b>UID :</b> {{ $RMInventoryAdjustment->uid }}</p>
                                            <p><b>Date :</b> {{ $RMInventoryAdjustment->date }} </p>
                                            <p><b>Store :</b> {{ $RMInventoryAdjustment->store->name }} </p>
                                            <p><b>Transaction Type :</b> {!! showStatus($RMInventoryAdjustment->transaction_type) !!}</p>
                                            <p><b>Status :</b> {!! showStatus($RMInventoryAdjustment->status) !!}</p>
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
                                        <td>{{ $item->coi->parent->name ?? '' }}</td>
                                        <td>{{ $item->coi->name ?? '' }}</td>
                                        <td>{{ $item->coi->unit->name ?? '' }}</td>
                                        <td>{{ $item->rate ?? '' }}</td>
                                        <td>{{ $item->quantity ?? '' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
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
