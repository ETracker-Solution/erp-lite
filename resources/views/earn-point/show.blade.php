@extends('layouts.app')

@section('title', 'Earn Point Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Earn Point' => route('earn-points.index'),
            'Details' => '',
        ];
        $pointsFmt = number_format((float) $earnPoint->point, 2);
    @endphp
    <x-breadcrumb title="Earn Point Details" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Earn Point #{{ $earnPoint->id }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('earn-points.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Customer</div>
                                    <div class="font-weight-bold">{{ $earnPoint->customer->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Mobile</div>
                                    <div class="font-weight-bold">{{ $earnPoint->customer->mobile ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Invoice</div>
                                    <div class="font-weight-bold">{{ $earnPoint->sale->invoice_number ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Points Earned</div>
                                    <div class="font-weight-bold text-success" style="font-size:1.25rem;">{{ $pointsFmt }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Sale Date</div>
                                    <div class="font-weight-bold">{{ $earnPoint->sale->date ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Sale Total</div>
                                    <div class="font-weight-bold">{{ isset($earnPoint->sale->grand_total) ? number_format((float) $earnPoint->sale->grand_total, 2) : '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Recorded At</div>
                                    <div class="font-weight-bold">{{ $earnPoint->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
