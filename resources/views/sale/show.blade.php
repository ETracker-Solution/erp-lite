@extends('layouts.app')
@section('title')
    Sales Details
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Sales Details'=>''
        ]
    @endphp
    <x-breadcrumb title='Sales Details' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Sales Details</h3>
                            <a href="{{route('sale.pdf-download',encrypt($sale->id))}}"
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
                                            <p><b>Outlet :</b> {{ $sale->outlet->name }}</p>
                                            <p><b>Customer Name
                                                    :</b> {{ $sale->customer ? $sale->customer->name : 'N/A' }} </p>
                                            <p><b>Customer Number
                                                    :</b> {{ $sale->customer ? $sale->customer->mobile ?? 'N/A' : 'N/A' }}
                                            </p>
                                            {{--                                            @if(isset($sale->delivery_time))--}}
                                            {{--                                                <b>Delivery Time :</b> {{ \Carbon\Carbon::parse($sale->delivery_time)->format('h:i A') }}--}}
                                            {{--                                            @else--}}

                                            {{--                                            @endif--}}
                                            {{--                                            <p><b>Status :</b> {!! showStatus($sale->status) !!}</p>--}}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">
                                <table width="100%">
                                    <tbody>
                                    <tr>
                                        <td style="text-align: left; padding:8px; line-height: 0.6">
                                            <p><b>Address :</b> {{ $sale->outlet->address }}</p>
                                            <p><b>Status :</b> {{ $sale->status }}</p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 invoice-col">
                                <table width="100%">
                                    <tbody>
                                    <tr>
                                        <td style="text-align: left; padding:8px; line-height: 0.6">
                                            <p><b>Customer :</b> {{ $sale->customer->name }}</p>
                                            <p><b>email :</b> {{ $sale->customer->email }} </p>
                                            <p><b>Phone :</b> {{ $sale->customer->mobile }} </p>
                                            <p><b>Address :</b> {{ $sale->customer->address }}</p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
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
                                    @foreach ($sale->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sale->invoice_number }}</td>
                                            <td>{{ $item->coi->name ?? '' }}</td>
                                            <td>{{ $item->unit_price ?? 0 }}</td>
                                            <td>{{ $item->quantity ?? 0 }}</td>
                                            <td>{{ $item->discount ?? 0 }}</td>
                                            <td class="text-right">{{ ($item->unit_price * $item->quantity) - $item->discount }}</td>
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
                                            <td class="text-right">{{ $sale->subtotal }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Delivery Charge:</th>
                                            <td class="text-right">{{ $sale->delivery_charge }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Additional Charge:</th>
                                            <td class="text-right">{{ $sale->additional_charge }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Discount:</th>
                                            <td class="text-right">{{ $sale->discount }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Grand Total</th>
                                            <td class="text-right">{{ $sale->grand_total }}</td>
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
            @if(getTimeByFormat($sale->created_at,'Y-m-d') == date('Y-m-d') && (auth()->user()->is_super || (auth()->user()->employee && auth()->user()->employee->user_of == 'ho')))
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <form action="{{ route('sales.destroy',$sale->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" id="salesDelete">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    <!-- /.content-wrapper -->
@endsection
@push('js_scripts')
    <script>
        $(document).ready(() => {
            confirmAlert('#salesDelete')
        })
    </script>
@endpush
