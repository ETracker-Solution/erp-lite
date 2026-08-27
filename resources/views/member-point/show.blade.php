@extends('layouts.app')

@section('title', 'Point Setting Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Point Settings' => route('member-points.index'),
            'Details' => '',
        ];
    @endphp
    <x-breadcrumb title="Point Setting Details" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Point Setting #{{ $memberPoint->id }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('member-points.edit', encrypt($memberPoint->id)) }}" class="btn btn-sm btn-warning mr-1">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('member-points.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Member Type</div>
                                    <div class="font-weight-bold">{{ $memberPoint->memberType->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Per Amount</div>
                                    <div class="font-weight-bold">{{ number_format((float) $memberPoint->per_amount, 2) }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Point</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">{{ number_format((float) $memberPoint->point, 2) }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Created At</div>
                                    <div class="font-weight-bold">{{ $memberPoint->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
