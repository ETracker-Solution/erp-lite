@extends('layouts.app')

@section('title', 'FG Requisition Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'FG Requisition' => route('requisitions.index'),
            'Details' => '',
        ];
    @endphp
    <x-breadcrumb title="FG Requisition Details" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                FGR {{ $requisition->uid ?: ('#'.$requisition->id) }}
                                <span class="ml-2">{!! showStatus($requisition->status) !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('requisitions.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="{{ route('requisition.pdf', encrypt($requisition->id)) }}"
                                   class="btn btn-sm btn-secondary" target="_blank" rel="noopener">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $requisition->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">From Store</div>
                                    <div class="font-weight-bold">{{ $requisition->fromStore->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">To Store</div>
                                    <div class="font-weight-bold">{{ $requisition->toStore->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Outlet</div>
                                    <div class="font-weight-bold">{{ $requisition->outlet->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Created By</div>
                                    <div class="font-weight-bold">{{ showUserInfo($requisition->createdBy) }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Approved By</div>
                                    <div class="font-weight-bold">{{ $requisition->approvedBy ? showUserInfo($requisition->approvedBy) : '—' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-muted text-uppercase">Remarks</div>
                                    <div class="font-weight-bold">{{ $requisition->remark ?: '—' }}</div>
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
                                        <th class="text-right">Req Qty</th>
                                        <th class="text-right">Delivered</th>
                                        <th class="text-right">Remaining</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($requisition->items as $item)
                                        @php
                                            $reqQty = (float) ($item->quantity ?? 0);
                                            $deliveredQty = (float) ($deliveredQtyByCoi[$item->coi_id] ?? 0);
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '—' }}</td>
                                            <td>{{ $item->coi->name ?? '—' }}</td>
                                            <td>{{ $item->coi->unit->name ?? '—' }}</td>
                                            <td class="text-right">{{ number_format($reqQty, 2) }}</td>
                                            <td class="text-right">{{ number_format($deliveredQty, 2) }}</td>
                                            <td class="text-right">{{ number_format(max($reqQty - $deliveredQty, 0), 2) }}</td>
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
