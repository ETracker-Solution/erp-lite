@extends('layouts.app')

@section('title', 'Customer Receive Voucher Details')
@section('content')
    @push('style')
    @endpush
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Accounts Module'=>'',
    'Customer Receive Voucher List'=>route('customer-receive-vouchers.index'),
    'Details'=>'',
    ]
    @endphp
    <x-breadcrumb title='Customer Receive Voucher Details' :links="$links"/>

    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Customer Receive Voucher Details</h4>
                            <div class="card-tools">
                                <a href="{{route('customer-receive-vouchers.index')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </button>
                                </a>
                                <a href="{{ route('customer-receive-voucher.pdf', encrypt($voucher->id)) }}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-download"></i> PDF</a>
                            </div>
                        </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong>Date :</strong></th>
                                        <td>{{ $voucher->date }}</td>
                                        <th><strong>CRV No :</strong></th>
                                        <td>{{ $voucher->uid }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Description :</strong></th>
                                        <td colspan="3">{{ $voucher->narration }}</td>
                                    </tr>
                                </thead>
                            </table>
                            <h5 class="mt-4">Receive Information</h5>
                            <table class="table table-bordered mt-3">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th>#</th>
                                        <th>Receive Mode</th>
                                        <th>Customer</th>
                                        <th>Invoice No</th>
                                        <th>Amount</th>
                                        <th>Settle Discount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $totalDiscount = 0;
                                    @endphp
                                    @foreach($customerReceiveVouchers as $index => $item)
                                    @php
                                        $total += $item->amount;
                                        $totalDiscount += $item->settle_discount;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->debitAccount->name ?? '' }}</td>
                                        <td>{{ $item->customer->name ?? '' }}</td>
                                        <td>{{ $item->sale ? $item->sale->invoice_number : 'N/A' }}</td>
                                        <td>{{ number_format($item->amount, 2) }}</td>
                                        <td>{{ number_format($item->settle_discount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Total:</th>
                                        <th>{{ number_format($total, 2) }}</th>
                                        <th>{{ number_format($totalDiscount, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->

@endsection
@section('css')

@endsection
@section('js')

@endsection
