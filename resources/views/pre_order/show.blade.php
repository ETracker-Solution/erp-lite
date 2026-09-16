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
            : 'N/A';
        $customerMobile = ($model->customer?->type ?? '') === 'default'
            ? 'N/A'
            : ($model->customer?->mobile ?: 'N/A');
        $deliveryCharge = (float) ($model->sale->delivery_charge ?? 0);
        $additionalCharge = (float) ($model->sale->additional_charge ?? 0);
        $attachments = $model->attachments ?? collect();
    @endphp
    <x-breadcrumb title="Pre Order" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info pre-order-print">
                        <div class="card-header">
                            <h3 class="card-title">Pre Order Details</h3>
                            <div class="card-tools">
                                <a href="{{ route('pre-orders.index') }}"
                                   class="btn btn-sm btn-primary d-print-none">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="{{ route('pre-order.pdf', $model->id) }}"
                                   class="btn btn-sm btn-primary" target="_blank">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>

                        {{-- Previous print-friendly two-column layout --}}
                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col pl-4" style="padding: 10px">
                                <b>Delivery Date :</b> {{ $model->delivery_date ?: 'N/A' }}, <br>
                                <b>Delivery Time :</b> {{ $deliveryTime }}, <br>
                                <b>Size and Shape:</b> {{ $model->size }}, <br>
                                <b>Flavour :</b> {{ $model->flavour }}, <br>
                                <b>Cake Message :</b> {{ $model->cake_message }}, <br>
                                <b>Description :</b> {{ $model->remark }}.
                            </div>
                            <div class="col-sm-4 invoice-col pl-4" style="padding: 10px">
                                <b>Order No :</b> {{ $model->order_number }}, <br>
                                <b>Customer :</b> {{ $model->customer->name ?? '' }}, <br>
                                <b>Customer Number:</b> {{ $customerMobile }}, <br>
                                <b>From Outlet :</b> {{ $model->outlet->name ?? '' }}, <br>
                                <b>Delivery Point :</b> {{ $model->deliveryPoint->name ?? '' }}, <br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Group</th>
                                        <th>Item</th>
                                        <th>Rate</th>
                                        <th>Qty</th>
                                        <th>Discount</th>
                                        <th class="text-right">Item Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($model->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->coi->parent->name ?? '' }}</td>
                                            <td>{{ $item->coi->name ?? '' }}</td>
                                            <td>{{ $item->unit_price }}</td>
                                            <td>
                                                {{ $item->quantity ?? '' }}
                                                {{ $item->coi->unit->name ?? ($item->product->unit->name ?? '') }}
                                            </td>
                                            <td>{{ $item->discount }}</td>
                                            <td class="text-right">
                                                <b>{{ $item->unit_price * $item->quantity }}</b>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-8"></div>
                            <div class="col-4">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">Subtotal:</th>
                                            <td class="text-right">{{ $model->subtotal }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Delivery Charge:</th>
                                            <td class="text-right">{{ $deliveryCharge }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Additional Charge:</th>
                                            <td class="text-right">{{ $additionalCharge }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Discount:</th>
                                            <td class="text-right">{{ $model->discount }}</td>
                                        </tr>
                                        <tr>
                                            <th style="width:50%">Grand Total</th>
                                            <td class="text-right">{{ $model->grand_total }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 invoice-col px-3 pb-3">
                                @if ($attachments->isNotEmpty())
                                    @foreach($attachments as $attachment)
                                        <a target="_blank" href="{{ asset('/upload/'.$attachment->image) }}">
                                            <img src="{{ asset('/upload/'.$attachment->image) }}"
                                                 class="rounded" alt="" width="40%">
                                        </a>
                                    @endforeach
                                @elseif($model->image)
                                    <a target="_blank" href="{{ asset('/upload/'.$model->image) }}">
                                        <img src="{{ asset('/upload/'.$model->image) }}"
                                             class="rounded" alt="" width="40%">
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style')
    <style>
        @media print {
            .main-sidebar,
            .main-header,
            .content-header,
            .breadcrumb,
            .erp-breadcrumb,
            .d-print-none,
            .btn,
            .card-tools {
                display: none !important;
            }

            .content-wrapper,
            .content,
            .container-fluid,
            .card,
            .card-body {
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                box-shadow: none !important;
                background: #fff !important;
            }

            .pre-order-print .card-header {
                border: 0 !important;
                background: transparent !important;
            }

            .pre-order-print img {
                max-width: 45% !important;
                height: auto !important;
                page-break-inside: avoid;
            }
        }
    </style>
@endpush
