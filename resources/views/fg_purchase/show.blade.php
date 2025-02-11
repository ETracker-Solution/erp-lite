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
                <h1>Goods Purchase Bill</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Goods Purchase Details</li>
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
                    <div class="card-header">
                        <h3 class="card-title">Goods Purchase Details</h3>
                        <a href="{{ route('fg-purchase.pdf-download', encrypt($model->id)) }}"
                            class="btn btn-sm btn-primary float-right" target="_blank"><i class="fa fa-download"></i>
                            PDF</a>
                    </div>
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> Cake Town.
                                <small class="float-right">Date:{{ $model->created_at }}</small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            <address class="pl-3">
                                Address : {{ getSettingValue('company_address') }} <br>
                                Phone : {{ getSettingValue('company_phone') }}<br>
                                Email : {{ getSettingValue('company_email') }}
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">

                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            GPB No : {{ $model->uid }} <br>
                            Name : {{ $model->supplier->name }} <br>
                            Address : {{ $model->supplier->address }}<br>
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
                                        <th>Product</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th class="text-right">Item Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '' }}</td>
                                            <td>{{ $item->coi->name ?? '' }}</td>

                                            <td>
                                                {{ $item->quantity ?? '' }} {{ $item->coi->unit->name ?? '' }}
                                                @if (isset($item->coi->a_unit_quantity) && $item->coi->a_unit_quantity > 0 && $item->coi->alter_unit_id)
                                                    ({{ $item->quantity / $item->coi->a_unit_quantity }} {{ $item->coi->alterUnit->name ?? '' }})
                                                @endif
                                            </td>

                                            <td>{{ $item->rate ?? '' }}</td>
                                            <td class="text-right">{{ $item->rate * $item->quantity ?? '' }}</td>
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
                                        <th style="width:50%">Discount:</th>
                                        <td class="text-right">{{ $model->discount }}</td>
                                        </tr>
                                        <tr>
                                        <th style="width:50%">Subtotal:</th>
                                        <td class="text-right">{{ $model->subtotal }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

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