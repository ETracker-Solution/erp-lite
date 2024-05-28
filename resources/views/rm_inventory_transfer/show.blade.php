@extends('layouts.app')
@section('title')
RM Inventory Transfer Details
@endsection
@section('content')
    @php    
        $links = [
        'Home'=>route('dashboard'),
        'RM Inventory Transfer'=>''
        ]
    @endphp
<x-breadcrumb title='RM Inventory Transfer Details' :links="$links"/>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">  
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">RM Inventory Transfer Details</h3>
                        <a href="{{route('rm-inventory-transfers.pdf',$RMInventoryTransfer->id)}}">
                            <button class="btn btn-sm btn-primary float-right"><i class="fa fa-download"
                                                                      aria-hidden="true"></i> &nbsp;PDF
                            </button>
                        </a>
                    </div> 
                    <!-- Main content -->
                    <div class="invoice p-3 mb-3">
                        <!-- title row -->
                        <!-- info row -->
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                                <table width="100%">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left; padding:8px; line-height: 0.6">
                                                <p><b>UID :</b> {{ $RMInventoryTransfer->uid }}</p>
                                                <p><b>Date :</b> {{ $RMInventoryTransfer->date }} </p>
                                                <p><b>Transfer From :</b> {{ $RMInventoryTransfer->fromStore->name }} </p>
                                                <p><b>Transfer To :</b> {{ $RMInventoryTransfer->toStore->name }} </p>
                                                <p><b>Status :</b> {!! showStatus($RMInventoryTransfer->status) !!}</p>
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
                </div>
                <!-- /.invoice -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->
@endsection
