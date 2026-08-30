@extends('layouts.app')

@section('title', 'Pre Order Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Pre Orders' => route('pre-orders.index'),
            'Details' => '',
        ];
        $deliveryTime = $model->delivery_time
            ? \Carbon\Carbon::parse($model->delivery_time)->format('h:i A')
            : '—';
        $customerMobile = ($model->customer?->type ?? '') === 'default'
            ? null
            : $model->customer?->mobile;
        $deliveryCharge = (float) ($model->sale->delivery_charge ?? 0);
        $additionalCharge = (float) ($model->sale->additional_charge ?? 0);
    @endphp
    <x-breadcrumb title="Pre Order" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Order {{ $model->order_number ?: ('#'.$model->id) }}
                                <span class="ml-2">{!! showStatus($model->status) !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('pre-orders.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="{{ route('pre-order.pdf', $model->id) }}"
                                   class="btn btn-sm btn-secondary" target="_blank">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Order Date</div>
                                    <div class="font-weight-bold">{{ $model->order_date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Delivery Date</div>
                                    <div class="font-weight-bold">{{ $model->delivery_date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Delivery Time</div>
                                    <div class="font-weight-bold">{{ $deliveryTime }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Advance Paid</div>
                                    <div class="font-weight-bold">{{ number_format((float) $model->advance_amount, 2) }}</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Customer</div>
                                    <div class="font-weight-bold">
                                        {{ $model->customer->name ?? '—' }}
                                        @if($customerMobile)
                                            <span class="text-muted">({{ $customerMobile }})</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">From Outlet</div>
                                    <div class="font-weight-bold">{{ $model->outlet->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Delivery Point</div>
                                    <div class="font-weight-bold">{{ $model->deliveryPoint->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Grand Total</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">
                                        {{ number_format((float) $model->grand_total, 2) }}
                                    </div>
                                </div>
                            </div>

                            @if($model->size || $model->flavour || $model->cake_message || $model->remark)
                                <div class="row mb-3">
                                    @if($model->size)
                                        <div class="col-md-3">
                                            <div class="small text-muted text-uppercase">Size &amp; Shape</div>
                                            <div class="font-weight-bold">{{ $model->size }}</div>
                                        </div>
                                    @endif
                                    @if($model->flavour)
                                        <div class="col-md-3">
                                            <div class="small text-muted text-uppercase">Flavour</div>
                                            <div class="font-weight-bold">{{ $model->flavour }}</div>
                                        </div>
                                    @endif
                                    @if($model->cake_message)
                                        <div class="col-md-3">
                                            <div class="small text-muted text-uppercase">Cake Message</div>
                                            <div class="font-weight-bold">{{ $model->cake_message }}</div>
                                        </div>
                                    @endif
                                    @if($model->remark)
                                        <div class="col-md-3">
                                            <div class="small text-muted text-uppercase">Remark</div>
                                            <div class="font-weight-bold">{{ $model->remark }}</div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width:5%">#</th>
                                        <th>Group</th>
                                        <th>Item</th>
                                        <th>Unit</th>
                                        <th class="text-right">Qty</th>
                                        <th class="text-right">Rate</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Value</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($model->items as $item)
                                        @php
                                            $qty = (float) ($item->quantity ?? 0);
                                            $rate = (float) ($item->unit_price ?? 0);
                                            $discount = (float) ($item->discount ?? 0);
                                            $lineTotal = ($qty * $rate) - $discount;
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '—' }}</td>
                                            <td>{{ $item->coi->name ?? '—' }}</td>
                                            <td>{{ $item->coi->unit->name ?? ($item->product->unit->name ?? '—') }}</td>
                                            <td class="text-right">{{ number_format($qty, 2) }}</td>
                                            <td class="text-right">{{ number_format($rate, 2) }}</td>
                                            <td class="text-right">{{ number_format($discount, 2) }}</td>
                                            <td class="text-right">{{ number_format($lineTotal, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No items</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                    <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="7" class="text-right">Subtotal</th>
                                        <th class="text-right">{{ number_format((float) $model->subtotal, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" class="text-right">Delivery Charge</th>
                                        <th class="text-right">{{ number_format($deliveryCharge, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" class="text-right">Additional Charge</th>
                                        <th class="text-right">{{ number_format($additionalCharge, 2) }}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="7" class="text-right">Discount</th>
                                        <th class="text-right">{{ number_format((float) $model->discount, 2) }}</th>
                                    </tr>
                                    <tr class="bg-light">
                                        <th colspan="7" class="text-right">Grand Total</th>
                                        <th class="text-right">{{ number_format((float) $model->grand_total, 2) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                            @if($model->attachments->isNotEmpty() || $model->image)
                                <div>
                                    <h6 class="font-weight-bold mb-2">Attachments</h6>
                                    <div class="d-flex flex-wrap">
                                        @foreach($model->attachments as $attachment)
                                            <a href="{{ asset('/upload/'.$attachment->image) }}"
                                               target="_blank" class="mr-2 mb-2">
                                                <img src="{{ asset('/upload/'.$attachment->image) }}"
                                                     alt="Attachment" class="img-thumbnail"
                                                     style="max-height:120px;">
                                            </a>
                                        @endforeach
                                        @if($model->image && $model->attachments->isEmpty())
                                            <a href="{{ asset('/upload/'.$model->image) }}" target="_blank" class="mb-2">
                                                <img src="{{ asset('/upload/'.$model->image) }}"
                                                     alt="Pre order image" class="img-thumbnail"
                                                     style="max-height:120px;">
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
