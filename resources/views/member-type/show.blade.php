@extends('layouts.app')

@section('title', 'Member Type Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Member Type' => route('member-types.index'),
            'Details' => '',
        ];
    @endphp
    <x-breadcrumb title="Member Type Details" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">{{ $memberType->name }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('member-types.edit', encrypt($memberType->id)) }}" class="btn btn-sm btn-warning mr-1">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('member-types.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">From Point</div>
                                    <div class="font-weight-bold">{{ number_format((float) $memberType->from_point, 0) }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">To Point</div>
                                    <div class="font-weight-bold">{{ number_format((float) $memberType->to_point, 0) }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Minimum Purchase</div>
                                    <div class="font-weight-bold">{{ number_format((float) $memberType->minimum_purchase, 2) }}</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Discount</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">{{ number_format((float) $memberType->discount, 2) }}%</div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="small text-muted text-uppercase">Created At</div>
                                    <div class="font-weight-bold">{{ $memberType->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
