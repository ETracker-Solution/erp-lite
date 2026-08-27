@extends('layouts.app')

@section('title', 'Sales Delivery Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Sales Delivery' => route('sales-deliveries.index'),
            'Details' => '',
        ];
        $grandFmt = number_format((float) $sale->grand_total, 2);
        $dueFmt = number_format(max((float) $sale->grand_total - ((float) $sale->receive_amount + (float) $sale->delivery_point_receive_amount), 0), 2);
    @endphp
    <x-breadcrumb title="Sales Delivery" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Invoice {{ $sale->invoice_number ?: ('#'.$sale->id) }}
                                <span class="ml-2">{!! showStatus($sale->status) !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('sales-deliveries.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                                @if($sale->status === 'pending')
                                    <a href="{{ route('sales-deliveries.create', ['sale_id' => $sale->id]) }}"
                                       class="btn btn-sm btn-success">
                                        <i class="fa fa-truck"></i> Deliver
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $sale->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Order From</div>
                                    <div class="font-weight-bold">{{ $sale->outlet->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Delivery Point</div>
                                    <div class="font-weight-bold">{{ $sale->deliveryPoint->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Customer</div>
                                    <div class="font-weight-bold">{{ $sale->customer->name ?? '—' }}
                                        @if($sale->customer?->mobile)
                                            <span class="text-muted">({{ $sale->customer->mobile }})</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Grand Total</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">{{ $grandFmt }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Due</div>
                                    <div class="font-weight-bold">{{ $dueFmt }}</div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width:5%">#</th>
                                        <th>Group</th>
                                        <th>Item</th>
                                        <th>Unit</th>
                                        <th class="text-right">Qty</th>
                                        <th class="text-right">Rate</th>
                                        <th class="text-right">Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($sale->items as $item)
                                        @php
                                            $qty = (float) ($item->quantity ?? 0);
                                            $rate = (float) ($item->unit_price ?? $item->rate ?? 0);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '—' }}</td>
                                            <td>{{ $item->coi->name ?? '—' }}</td>
                                            <td>{{ $item->coi->unit->name ?? '—' }}</td>
                                            <td class="text-right">{{ number_format($qty, 2) }}</td>
                                            <td class="text-right">{{ number_format($rate, 2) }}</td>
                                            <td class="text-right">{{ number_format($qty * $rate, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No items</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
