@extends('layouts.app')

@section('title', 'Membership Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Membership' => route('memberships.index'),
            'Details' => '',
        ];
    @endphp
    <x-breadcrumb title="Membership Details" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                {{ $membership->membership_number ?: ('Membership #' . $membership->id) }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('memberships.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Customer</div>
                                    <div class="font-weight-bold">{{ $membership->customer->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Mobile</div>
                                    <div class="font-weight-bold">{{ $membership->customer->mobile ?? '—' }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Member Type</div>
                                    <div class="font-weight-bold">{{ $membership->memberType->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Points</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">{{ number_format((float) $membership->point, 2) }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Type Discount</div>
                                    <div class="font-weight-bold">{{ isset($membership->memberType->discount) ? number_format((float) $membership->memberType->discount, 2) . '%' : '—' }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Created At</div>
                                    <div class="font-weight-bold">{{ $membership->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
