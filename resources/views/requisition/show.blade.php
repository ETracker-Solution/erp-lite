@extends('layouts.app')
@section('title')
Requisition Details
@endsection
@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
    @php    
        $links = [
        'Home'=>route('dashboard'),
        'Requisition Details'=>''
        ]
    @endphp
<x-breadcrumb title='Requisition Details' :links="$links"/>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Main content -->
                <div class="invoice p-3 mb-3">
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td style="text-align: left; padding:8px; line-height: 0.6">
                                            <p><b>Requisition No :</b> {{ $requisition->uid }}</p>
                                            <p><b>Date :</b> {{ $requisition->date }} </p>
                                            <p><b>Sub Total :</b> {{ $requisition->subtotal }} </p>
                                            <p><b>Status :</b> {{ $requisition->status }}</p>
                                        </td> 
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <td><b>Customer :</b> {{ $requisition->customer->name??'Walking Customer'}}</td>
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
                                    @foreach ($requisition->items as $item)
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
                                        <td>{{ $requisition->subtotal }} TK</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.invoice -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- /.content-wrapper -->
@endsection
