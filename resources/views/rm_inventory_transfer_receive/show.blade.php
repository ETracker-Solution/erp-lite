@extends('layouts.app')

@section('title', 'RM Inventory Transfer Receive Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'RM Transfer Receive' => route('rm-transfer-receives.index'),
            'Details' => '',
        ];
    @endphp
    <x-breadcrumb title="RM Transfer Receive" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Receive {{ $rmTransferReceive->uid ?: ('#'.$rmTransferReceive->id) }}
                                <span class="ml-2">{!! showStatus($rmTransferReceive->status) !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('rm-transfer-receives.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="{{ route('rm-transfer-receive.pdf', encrypt($rmTransferReceive->id)) }}"
                                   class="btn btn-sm btn-secondary" target="_blank" rel="noopener">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $rmTransferReceive->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Transfer UID</div>
                                    <div class="font-weight-bold">{{ $rmTransferReceive->inventoryTransfer->uid ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">From Store</div>
                                    <div class="font-weight-bold">{{ $rmTransferReceive->fromStore->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">To Store</div>
                                    <div class="font-weight-bold">{{ $rmTransferReceive->toStore->name ?? '—' }}</div>
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
                                    @forelse ($rmTransferReceive->items as $item)
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
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
