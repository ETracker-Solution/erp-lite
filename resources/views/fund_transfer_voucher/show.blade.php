@extends('layouts.app')

@section('title', 'Fund Transfer Voucher Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Accounts Module' => '',
            'General Accounts' => '',
            'Fund Transfer Voucher' => route('fund-transfer-vouchers.index'),
            'Details' => '',
        ];
        $status = strtolower((string) ($fundTransferVoucher->status ?? 'pending'));
        $statusClass = $status === 'received' ? 'success' : ($status === 'pending' ? 'warning' : 'secondary');
    @endphp
    <x-breadcrumb title="Fund Transfer Voucher" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                FTV #{{ $fundTransferVoucher->uid ?: $fundTransferVoucher->id }}
                                <span class="badge badge-{{ $statusClass }} ml-2">{{ ucfirst($status) }}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('fund-transfer-vouchers.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> See List
                                </a>
                                <a href="{{ route('fund-transfer-voucher.pdf', encrypt($fundTransferVoucher->id)) }}"
                                   class="btn btn-sm btn-secondary" target="_blank" rel="noopener">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="small text-muted">Date</div>
                                    <div class="font-weight-bold">{{ $fundTransferVoucher->date }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="small text-muted">Amount</div>
                                    <div class="font-weight-bold text-primary" style="font-size:1.25rem;">
                                        {{ number_format((float) $fundTransferVoucher->amount, 2) }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="small text-muted">Created By</div>
                                    <div class="font-weight-bold">
                                        {{ $fundTransferVoucher->createdBy->name ?? '—' }}
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered mb-0">
                                <tbody>
                                <tr>
                                    <th style="width:35%">Transfer From</th>
                                    <td>{{ $fundTransferVoucher->creditAccount->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Transfer To</th>
                                    <td>{{ $fundTransferVoucher->debitAccount->name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Reference No</th>
                                    <td>{{ $fundTransferVoucher->reference_no ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Remark</th>
                                    <td>{{ $fundTransferVoucher->narration ?: '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ optional($fundTransferVoucher->created_at)->format('Y-m-d H:i') ?: '—' }}</td>
                                </tr>
                                @if($status === 'received')
                                    <tr>
                                        <th>Received At</th>
                                        <td>{{ optional($fundTransferVoucher->updated_at)->format('Y-m-d H:i') ?: '—' }}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
