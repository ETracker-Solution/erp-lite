@extends('layouts.app')

@section('title', 'Supplier Payment Voucher Details')

@section('content')
    @php
        $links = [
            'Home' => route('dashboard'),
            'Accounts Module' => '',
            'General Accounts' => '',
            'Supplier Payment Voucher' => route('supplier-vouchers.index'),
            'Details' => '',
        ];
        $amountFmt = number_format((float) $supplierVoucher->amount, 2);
        $voucherDate = $supplierVoucher->date;
        if ($voucherDate instanceof \Carbon\CarbonInterface) {
            $voucherDate = $voucherDate->format('d M Y');
        }
    @endphp
    <x-breadcrumb title="Supplier Payment Voucher" :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title mb-0">
                                Supplier Payment Voucher
                                <span class="ml-2 font-weight-normal">{{ $supplierVoucher->uid ?: ('#'.$supplierVoucher->id) }}</span>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('supplier-vouchers.index') }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-list"></i> List
                                </a>
                                <a href="{{ route('supplier-voucher.pdf', encrypt($supplierVoucher->id)) }}"
                                   class="btn btn-sm btn-secondary" target="_blank" rel="noopener">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Voucher No</div>
                                    <div class="font-weight-bold" style="font-size:1.15rem;">
                                        {{ $supplierVoucher->uid ?: ('#'.$supplierVoucher->id) }}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Voucher Date</div>
                                    <div class="font-weight-bold">{{ $voucherDate ?: '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="small text-muted text-uppercase">Supplier</div>
                                    <div class="font-weight-bold">{{ $supplierVoucher->supplier->name ?? '—' }}</div>
                                </div>
                                <div class="col-md-3 text-md-right">
                                    <div class="small text-muted text-uppercase">Amount</div>
                                    <div class="font-weight-bold text-success" style="font-size:1.4rem;">
                                        {{ $amountFmt }}
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="small text-muted text-uppercase">Paid To</div>
                                    <div>{{ $supplierVoucher->payee_name ?: '—' }}</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="small text-muted text-uppercase">Reference No</div>
                                    <div>{{ $supplierVoucher->reference_no ?: '—' }}</div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered mb-2">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width:8%">#</th>
                                        <th>Particulars (Account)</th>
                                        <th class="text-right" style="width:18%">Debit</th>
                                        <th class="text-right" style="width:18%">Credit</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <div class="font-weight-bold">{{ $supplierVoucher->debitAccount->name ?? '—' }}</div>
                                            <small class="text-muted">Dr — Accounts Payable</small>
                                        </td>
                                        <td class="text-right font-weight-bold">{{ $amountFmt }}</td>
                                        <td class="text-right text-muted">—</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
                                            <div class="font-weight-bold">{{ $supplierVoucher->creditAccount->name ?? '—' }}</div>
                                            <small class="text-muted">Cr — Payment Account (Cash / Bank)</small>
                                        </td>
                                        <td class="text-right text-muted">—</td>
                                        <td class="text-right font-weight-bold">{{ $amountFmt }}</td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="2" class="text-right">Total</th>
                                        <th class="text-right">{{ $amountFmt }}</th>
                                        <th class="text-right">{{ $amountFmt }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mb-3">
                                <div class="small text-muted text-uppercase">Amount in Words</div>
                                <div class="font-italic">{{ $supplierVoucher->amountInWords() }}</div>
                            </div>

                            <div class="mb-4">
                                <div class="small text-muted text-uppercase">Narration</div>
                                <div>{{ $supplierVoucher->narration ?: '—' }}</div>
                            </div>

                            <div class="row text-center mt-5 pt-3" style="border-top:1px dashed #dee2e6;">
                                <div class="col-4">
                                    <div style="border-top:1px solid #333; margin:3rem auto 0.35rem; width:80%;"></div>
                                    <small class="text-muted">Prepared By</small>
                                </div>
                                <div class="col-4">
                                    <div style="border-top:1px solid #333; margin:3rem auto 0.35rem; width:80%;"></div>
                                    <small class="text-muted">Checked By</small>
                                </div>
                                <div class="col-4">
                                    <div style="border-top:1px solid #333; margin:3rem auto 0.35rem; width:80%;"></div>
                                    <small class="text-muted">Approved By</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
