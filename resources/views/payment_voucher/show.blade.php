@extends('layouts.app')

@section('title', 'Payment Voucher')
@section('content')
@push('style')
@endpush
@php
    $links = [
    'Home'=>route('dashboard'),
    'Payment Voucher'=>route('payment-vouchers.index'),
    'Payment Voucher Datails'=>'',
    ]
    @endphp
    <x-breadcrumb title='Payment Voucher' :links="$links"/>
     <!-- Basic Inputs start -->
     <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Payment Voucher Details</h4>
                            <div class="text-right">
                                {{-- <a href="{{ route('payment-voucher.pdf', encrypt($paymentVoucher->id)) }}" class="btn btn-primary" target="_blank"><i class="fa fa-download"></i> PDF</a> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong>Date :</strong></th>
                                        <td>{{ $paymentVoucher->date }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Debit Account  :</strong></th>
                                        <td>{{ $paymentVoucher->debitAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Payment Account  :</strong></th>
                                        <td>{{ $paymentVoucher->cashBankAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Amount :</strong></th>
                                        <td>{{ $paymentVoucher->amount }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Description :</strong></th>
                                        <td>{{ $paymentVoucher->narration }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Referance :</strong></th>
                                        <td>{{ $paymentVoucher->reference_no }}</td>
                                    </tr>
                                    
                                </thead>
                            </table>
                        </div>
                        {{-- adjust modal --}}     
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
