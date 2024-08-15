@extends('layouts.app')

@section('title', 'Supplier Payment Voucher')
@section('content')
    @push('style')
    @endpush
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Accounts Module'=>'',
    'General Accounts'=>'',
    'Supplier Payment Voucher Edit'=>'',
    ]
    @endphp
    <x-breadcrumb title='Supplier Payment Voucher' :links="$links"/>

    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Supplier Payment Voucher Details</h4>
                            <div class="card-tools">
                                <a href="{{route('supplier-vouchers.index')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </button>
                                </a>
                                <a href="{{ route('supplier-voucher.pdf', encrypt($supplierVoucher->id)) }}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-download"></i> PDF</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong>Supplier  :</strong></th>
                                        <td>{{ $supplierVoucher->supplier->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Date :</strong></th>
                                        <td>{{ $supplierVoucher->date }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>SPV No :</strong></th>
                                        <td>{{ $supplierVoucher->uid }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Credit Account  :</strong></th>
                                        <td>{{ $supplierVoucher->creditAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Debit Account  :</strong></th>
                                        <td>{{ $supplierVoucher->debitAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Amount :</strong></th>
                                        <td>{{ $supplierVoucher->amount }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Receiver Name :</strong></th>
                                        <td>{{ $supplierVoucher->payee_name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Description :</strong></th>
                                        <td>{{ $supplierVoucher->narration }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Referance :</strong></th>
                                        <td>{{ $supplierVoucher->reference_no }}</td>
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
