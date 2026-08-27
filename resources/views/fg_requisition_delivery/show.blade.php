@extends('layouts.app')

@section('title', 'FG Requisition Delivery Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'FG Requisition Delivery' => route('fg-requisition-deliveries.index'),
            'Details' => '',
        ];
        $totalQty = number_format((float) ($fgRequisitionDelivery->total_quantity ?: $fgRequisitionDelivery->items->sum('quantity')), 2);
    @endphp
    <x-breadcrumb title="FG Requisition Delivery" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Delivery {{ $fgRequisitionDelivery->uid ?: ('#'.$fgRequisitionDelivery->id) }}
                                <span class="ml-2">{!! showStatus($fgRequisitionDelivery->status) !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('fg-requisition-deliveries.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="{{ route('fg-requisition-delivery.pdf', encrypt($fgRequisitionDelivery->id)) }}"
                                   class="btn btn-sm btn-secondary" target="_blank" rel="noopener">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">FGR No</div>
                                    <div class="font-weight-bold">{{ $fgRequisitionDelivery->requisition->uid ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $fgRequisitionDelivery->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">From Store</div>
                                    <div class="font-weight-bold">{{ $fgRequisitionDelivery->fromStore->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">To Store</div>
                                    <div class="font-weight-bold">{{ $fgRequisitionDelivery->toStore->name ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Outlet</div>
                                    <div class="font-weight-bold">{{ $fgRequisitionDelivery->requisition->outlet->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Delivered By</div>
                                    <div class="font-weight-bold">{{ showUserInfo($fgRequisitionDelivery->createdBy) }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Total Qty</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">{{ $totalQty }}</div>
                                </div>
                                @if($fgRequisitionDelivery->remark)
                                    <div class="col-md-3">
                                        <div class="small text-muted text-uppercase">Remark</div>
                                        <div>{{ $fgRequisitionDelivery->remark }}</div>
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
                                        <th class="text-right">Req Qty</th>
                                        <th class="text-right">Deliver Qty</th>
                                        <th class="text-right">Remaining</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($fgRequisitionDelivery->items as $item)
                                        @php
                                            $requisitionQty = (float) getRequisitionQty($item->requisition_id, $item->coi_id);
                                            $deliveryQty = (float) $item->quantity;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '—' }}</td>
                                            <td>{{ $item->coi->name ?? '—' }}</td>
                                            <td>{{ $item->coi->unit->name ?? '—' }}</td>
                                            <td class="text-right">{{ number_format($requisitionQty, 2) }}</td>
                                            <td class="text-right">{{ number_format($deliveryQty, 2) }}</td>
                                            <td class="text-right">{{ number_format(max($requisitionQty - $deliveryQty, 0), 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No items</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="5" class="text-right">Total Deliver Qty</th>
                                        <th class="text-right">{{ $totalQty }}</th>
                                        <th></th>
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
