@extends('layouts.app')

@section('title', 'FG Production Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'FG Production' => route('productions.index'),
            'Details' => '',
        ];
        $subtotalFmt = number_format((float) $production->subtotal, 2);
        $totalQty = number_format((float) ($production->total_quantity ?: $production->items->sum('quantity')), 2);
    @endphp
    <x-breadcrumb title="FG Production" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Production {{ $production->uid ?: ('#'.$production->id) }}
                                <span class="ml-2">{!! showStatus($production->status) !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('productions.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="{{ route('production.pdf', encrypt($production->id)) }}"
                                   class="btn btn-sm btn-secondary" target="_blank" rel="noopener">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $production->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Batch</div>
                                    <div class="font-weight-bold">{{ $production->batch->batch_no ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Factory</div>
                                    <div class="font-weight-bold">{{ $production->factory->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">FG Store</div>
                                    <div class="font-weight-bold">{{ $production->store->name ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Total Qty</div>
                                    <div class="font-weight-bold">{{ $totalQty }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Subtotal</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">{{ $subtotalFmt }}</div>
                                </div>
                                @if($production->remark)
                                    <div class="col-md-6">
                                        <div class="small text-muted text-uppercase">Remark</div>
                                        <div>{{ $production->remark }}</div>
                                    </div>
                                @endif
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
                                        <th class="text-right">Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($production->items as $item)
                                        @php
                                            $line = (float) ($item->quantity ?? 0) * (float) ($item->rate ?? 0);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '—' }}</td>
                                            <td>{{ $item->coi->name ?? '—' }}</td>
                                            <td>{{ $item->coi->unit->name ?? '—' }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->rate ?? 0), 2) }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->quantity ?? 0), 2) }}</td>
                                            <td class="text-right">{{ number_format($line, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No items</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="5" class="text-right">Total Qty / Subtotal</th>
                                        <th class="text-right">{{ $totalQty }}</th>
                                        <th class="text-right">{{ $subtotalFmt }}</th>
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
