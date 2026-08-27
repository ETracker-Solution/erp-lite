@extends('layouts.app')

@section('title', 'Delivery Cash Receive Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Delivery Cash Receive' => route('delivery-cash-receives.index'),
            'Details' => '',
        ];
    @endphp
    <x-breadcrumb title="Delivery Cash Receive Details" :links="$links"/>

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
                                <a href="{{ route('delivery-cash-receives.index') }}" class="btn btn-sm btn-primary">
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

                            @if($deliveryCashTransfer->status === 'pending')
                                <form action="{{ route('delivery-cash-receives.update', encrypt($deliveryCashTransfer->id)) }}"
                                      method="POST" class="mt-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success"
                                            onclick="return confirm('Mark this cash transfer as received?');">
                                        <i class="fa fa-check-circle"></i> Receive
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
