@extends('layouts.app')
@section('title')
FG Inventory Transfer Details
@endsection
@section('content')
    @php    
        $links = [
        'Home'=>route('dashboard'),
        'FG Inventory Transfer'=>''
        ]
    @endphp
<x-breadcrumb title='FG Inventory Transfer Details' :links="$links"/>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">  
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">FG Inventory Transfer Details</h3>
                        <a href="{{route('fg-inventory-transfers.pdf',encrypt($fGInventoryTransfer->id))}}"
                            class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i> PDF</a>
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
                                                <p><b>FGIT No :</b> {{ $fGInventoryTransfer->uid }}</p>
                                                <p><b>Date :</b> {{ $fGInventoryTransfer->date }} </p>
                                                <p><b>Transfer From :</b> {{ $fGInventoryTransfer->fromStore->name }} </p>
                                                <p><b>Transfer To :</b> {{ $fGInventoryTransfer->toStore->name }} </p>
                                                <p><b>Status :</b> {!! showStatus($fGInventoryTransfer->status) !!}</p>
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
