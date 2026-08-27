@extends('layouts.app')

@section('title', 'FG Delivery Receive Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'FG Delivery Receive' => route('fg-delivery-receives.index'),
            'Details' => '',
        ];
        $totalQty = number_format((float) ($fgDeliveryReceive->total_quantity ?: $fgDeliveryReceive->items->sum('quantity')), 2);
    @endphp
    <x-breadcrumb title="FG Delivery Receive" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Receive {{ $fgDeliveryReceive->requisitionDelivery->uid ?? ('#'.$fgDeliveryReceive->id) }}
                                <span class="ml-2">{!! showStatus($fgDeliveryReceive->status) !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('fg-delivery-receives.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="{{ route('fg-delivery-receive.pdf', encrypt($fgDeliveryReceive->id)) }}"
                                   class="btn btn-sm btn-secondary" target="_blank" rel="noopener">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">FGRD No</div>
                                    <div class="font-weight-bold">{{ $fgDeliveryReceive->requisitionDelivery->uid ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $fgDeliveryReceive->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">From Store</div>
                                    <div class="font-weight-bold">{{ $fgDeliveryReceive->fromStore->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">To Store</div>
                                    <div class="font-weight-bold">{{ $fgDeliveryReceive->toStore->name ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Delivered By</div>
                                    <div class="font-weight-bold">
                                        {{ $fgDeliveryReceive->requisitionDelivery ? showUserInfo($fgDeliveryReceive->requisitionDelivery->createdBy) : '—' }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Received By</div>
                                    <div class="font-weight-bold">{{ showUserInfo($fgDeliveryReceive->createdBy) }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Total Qty</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">{{ $totalQty }}</div>
                                </div>
                                @if($fgDeliveryReceive->remark)
                                    <div class="col-md-3">
                                        <div class="small text-muted text-uppercase">Remark</div>
                                        <div>{{ $fgDeliveryReceive->remark }}</div>
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
                                        <th class="text-right">Qty</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($fgDeliveryReceive->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '—' }}</td>
                                            <td>{{ $item->coi->name ?? '—' }}</td>
                                            <td>{{ $item->coi->unit->name ?? '—' }}</td>
                                            <td class="text-right">{{ number_format((float) ($item->quantity ?? 0), 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No items</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="4" class="text-right">Total Qty</th>
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
