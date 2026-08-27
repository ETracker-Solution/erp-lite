@extends('layouts.app')

@section('title', 'Purchase Return Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Purchase Return' => route('purchase-returns.index'),
            'Details' => '',
        ];
        $subtotalFmt = number_format((float) $purchaseReturn->subtotal, 2);
        $vatFmt = number_format((float) $purchaseReturn->vat, 2);
        $netFmt = number_format((float) $purchaseReturn->net_payable, 2);
    @endphp
    <x-breadcrumb title="Purchase Return Details" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                GPRB {{ $purchaseReturn->uid ?: ('#'.$purchaseReturn->id) }}
                                <span class="ml-2">{!! showStatus($purchaseReturn->status) !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('purchase-returns.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="{{ route('purchase_return.pdf', encrypt($purchaseReturn->id)) }}"
                                   class="btn btn-sm btn-secondary" target="_blank" rel="noopener">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $purchaseReturn->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Purchase No</div>
                                    <div class="font-weight-bold">{{ $purchaseReturn->purchase->uid ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Supplier</div>
                                    <div class="font-weight-bold">{{ $purchaseReturn->supplier->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Net Payable</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">{{ $netFmt }}</div>
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
                                        <th class="text-right">Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($purchaseReturn->items as $item)
                                        @php
                                            $line = (float) ($item->rate ?? 0) * (float) ($item->quantity ?? 0);
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
                                        <th colspan="6" class="text-right">Subtotal / VAT / Net Payable</th>
                                        <th class="text-right">{{ $subtotalFmt }} / {{ $vatFmt }} / {{ $netFmt }}</th>
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
