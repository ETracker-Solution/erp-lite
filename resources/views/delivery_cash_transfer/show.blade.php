@extends('layouts.app')

@section('title', 'Delivery Cash Transfer Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Delivery Cash Transfer' => route('delivery-cash-transfers.index'),
            'Details' => '',
        ];
    @endphp
    <x-breadcrumb title="Delivery Cash Transfer Details" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Invoice {{ $deliveryCashTransfer->invoice_number ?: ('#'.$deliveryCashTransfer->id) }}
                                <span class="ml-2">{!! showStatus($deliveryCashTransfer->status) !!}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('delivery-cash-transfers.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Date</div>
                                    <div class="font-weight-bold">{{ $deliveryCashTransfer->date ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Delivery Invoice</div>
                                    <div class="font-weight-bold">{{ $deliveryCashTransfer->invoice_number ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Other Outlet</div>
                                    <div class="font-weight-bold">{{ $deliveryCashTransfer->otherOutlet->outlet->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Amount</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">
                                        {{ number_format((float) $deliveryCashTransfer->amount, 2) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Transfer From</div>
                                    <div class="font-weight-bold">{{ $deliveryCashTransfer->creditAccount->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Transfer To</div>
                                    <div class="font-weight-bold">{{ $deliveryCashTransfer->debitAccount->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Reference</div>
                                    <div class="font-weight-bold">{{ $deliveryCashTransfer->reference_no ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Description</div>
                                    <div class="font-weight-bold">{{ $deliveryCashTransfer->narration ?: '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
