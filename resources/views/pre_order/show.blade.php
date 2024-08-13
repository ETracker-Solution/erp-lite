@extends('layouts.app')
@section('title')
    Purchase List
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2" style="background: #343A40; padding:8px; border-radius:6px; color:white">
                <div class="col-sm-6">
                    <h1>Pre Order</h1>
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
                    <!-- Main content -->
                    <div class="card card-info">
                        <!-- title row -->
                        <div class="card-header">
                            <h3 class="card-title">Pre Order Details</h3>
                            <a href="{{route('pre-order.pdf',$model->id)}}"
                               class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i>
                                PDF</a>
                        </div>
                        <!-- info row -->
                        <!-- /.row -->
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col pl-4 pt-4">
                                <b>Description :</b> {{ $model->remark }}
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">

                            </div>
                            <!-- /.col -->
                            <!-- /.col -->
                        </div>
                        <!-- Table row -->
                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Order No</th>
                                        <th>Customer</th>
                                        <th>Outlet</th>
                                        <th>Rate</th>
                                        <th>Qty</th>
                                        <th>Discount</th>
                                        <th class="text-right">Item Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($model->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $model->order_number }}</td>
                                            <td>{{ $model->customer->name }}</td>
                                            <td>{{ $model->outlet->name }}</td>
                                            <td>{{ $item->unit_price }}</td>
                                            <td>{{ $item->quantity?? '' }} {{ $item->product->unit->name?? '' }}</td>
                                            <td>{{ $item->discount }}</td>
                                            <td class="text-right">
                                                <b>{{ $item->unit_price * $item->quantity }} </b>
                                            </td>
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
                                            <td class="text-right">{{ $model->subtotal }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Discount:</th>
                                            <td class="text-right">{{ $item->discount }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Grand Total</th>
                                            <td class="text-right">{{ $model->grand_total - $item->discount }}</td>

                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                        <div class="row">
                            <div class="col-sm-12 invoice-col">
                                @if (isset($model->attachments))
                                    @foreach($model->attachments as $attachment)
                                        <a target="_blank" href="{{ asset('/upload/'.$attachment->image) }}"
                                           class="badge-light-info" target="_self">
                                            <img src="{{ asset('/upload/'.$attachment->image) }}" class="rounded"
                                                 alt="" width="40%">
                                        </a>
                                    @endforeach
                                @else
                                    <a target="_blank"
                                       href="{{ asset('admin/app-assets/dummy/dammy.jpg') }}"
                                       target="_self">
                                    <span class="b-avatar-img">
                                        <img src="{{ asset('admin/app-assets/dummy/dammy.jpg') }}"
                                             width="40%" alt="">
                                    </span>
                                    </a>
                                @endif

                            </div>
                        </div>
                        <!-- this row will not appear when printing -->
                    </div>
                    <!-- /.invoice -->
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <!-- /.content-wrapper -->
@endsection
