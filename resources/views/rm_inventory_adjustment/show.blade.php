@extends('layouts.app')

@section('title', 'RM Inventory Adjustment Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'RM Inventory Adjustment' => route('rm-inventory-adjustments.index'),
            'Details' => '',
        ];
        $totalQty = number_format((float) $items->sum('quantity'), 2);
    @endphp
    <x-breadcrumb title="RM Inventory Adjustment" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Adjustment {{ $RMInventoryAdjustment->uid ?: ('#'.$RMInventoryAdjustment->id) }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('rm-inventory-adjustments.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $RMInventoryAdjustment->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Store</div>
                                    <div class="font-weight-bold">{{ $RMInventoryAdjustment->store->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Txn Type</div>
                                    <div>{!! showStatus($RMInventoryAdjustment->transaction_type) !!}</div>
                                </div>
                                <div class="col-md-3 text-md-right">
                                    <div class="small text-muted text-uppercase">Qty</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">{{ $totalQty }}</div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width:6%">#</th>
                                        <th>Group</th>
                                        <th>Item</th>
                                        <th>Unit</th>
                                        <th class="text-right">Rate</th>
                                        <th class="text-right">Qty</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '—' }}</td>
                                            <td>{{ $item->coi->name ?? '—' }}</td>
                                            <td>{{ $item->coi->unit->name ?? '—' }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->rate ?? 0), 2) }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->quantity ?? 0), 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No items</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="5" class="text-right">Total Qty</th>
                                        <th class="text-right">{{ $totalQty }}</th>
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
