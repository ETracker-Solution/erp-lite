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
                                {{-- PDF Button if needed later --}}
                                {{-- <a href="{{ route('customer-receive-vouchers.pdf', encrypt($customerReceiveVoucher->id)) }}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-download"></i> PDF</a> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong>Date :</strong></th>
                                        <td>{{ $customerReceiveVoucher->date }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>CRV No :</strong></th>
                                        <td>{{ $customerReceiveVoucher->uid }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Customer :</strong></th>
                                        <td>{{ $customerReceiveVoucher->customer->name }} ({{ $customerReceiveVoucher->customer->mobile }})</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Invoice No :</strong></th>
                                        <td>{{ $customerReceiveVoucher->sale ? $customerReceiveVoucher->sale->invoice_number : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Received To (Debit Account) :</strong></th>
                                        <td>{{ $customerReceiveVoucher->debitAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Amount :</strong></th>
                                        <td>{{ $customerReceiveVoucher->amount }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Narration :</strong></th>
                                        <td>{{ $customerReceiveVoucher->narration }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Reference No :</strong></th>
                                        <td>{{ $customerReceiveVoucher->reference_no }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Created By :</strong></th>
                                        <td>{{ \App\Models\User::find($customerReceiveVoucher->created_by)->name ?? 'N/A' }}</td>
                                    </tr>
                                    
                                </thead>
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
