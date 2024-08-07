@extends('layouts.app')

@section('title', 'Fund Transfer Voucher')
@section('content')
@push('style')
@endpush
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Accounts Module'=>'',
    'General Accounts'=>'',
    'Fund Transfer Voucher Details'=>'',
    ]
    @endphp
    <x-breadcrumb title='Fund Transfer Voucher' :links="$links"/>
    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Fund Transfer Voucher Details</h4>
                            <div class="card-tools">
                                <a href="{{route('fund-transfer-vouchers.index')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </button>
                                </a>
                                {{-- <a href="{{ route('fund-transfer-voucher.pdf', encrypt($fundTransferVoucher->id)) }}" class="btn btn-primary" target="_blank"><i class="fa fa-download"></i> PDF</a> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong>Date :</strong></th>
                                        <td>{{ $fundTransferVoucher->date }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>FTV No :</strong></th>
                                        <td>{{ $fundTransferVoucher->uid }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Transfer From Account  :</strong></th>
                                        <td>{{ $fundTransferVoucher->debitAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Transfer To Account  :</strong></th>
                                        <td>{{ $fundTransferVoucher->creditAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Amount :</strong></th>
                                        <td>{{ $fundTransferVoucher->amount }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Description :</strong></th>
                                        <td>{{ $fundTransferVoucher->narration }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Referance :</strong></th>
                                        <td>{{ $fundTransferVoucher->reference_no }}</td>
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
