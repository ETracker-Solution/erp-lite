@extends('layouts.app')

@section('title', 'Sales Return Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Sales Return' => route('sales-returns.index'),
            'Details' => '',
        ];
        $subtotalFmt = number_format((float) $salesReturn->subtotal, 2);
        $discountFmt = number_format((float) $salesReturn->discount, 2);
        $grandFmt = number_format((float) $salesReturn->grand_total, 2);
    @endphp
    <x-breadcrumb title="Sales Return" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Return {{ $salesReturn->uid ?: ('#'.$salesReturn->id) }}
                                <span class="ml-2">{!! showStatus($salesReturn->status ?: 'final') !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('sales-returns.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $salesReturn->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Invoice</div>
                                    <div class="font-weight-bold">{{ $salesReturn->sale->invoice_number ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Subtotal</div>
                                    <div class="font-weight-bold">{{ $subtotalFmt }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Grand Total</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">{{ $grandFmt }}</div>
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
                                        <th class="text-right">Rate</th>
                                        <th class="text-right">Qty</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($salesReturn->items as $item)
                                        @php
                                            $line = ((float) ($item->rate ?? 0) * (float) ($item->quantity ?? 0))
                                                - (float) ($item->discount ?? 0);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '—' }}</td>
                                            <td>{{ $item->coi->name ?? '—' }}</td>
                                            <td>{{ $item->coi->unit->name ?? '—' }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->rate ?? 0), 2) }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->quantity ?? 0), 2) }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->discount ?? 0), 2) }}</td>
                                            <td class="text-right">{{ number_format($line, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No items</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="6" class="text-right">Discount / Grand Total</th>
                                        <th class="text-right">{{ $discountFmt }}</th>
                                        <th class="text-right">{{ $grandFmt }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
