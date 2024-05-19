@extends('layouts.app')

@section('title', 'Receive Voucher')
@section('content')
    @push('style')
    @endpush
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Receive Voucher'=>route('receive-vouchers.index'),
    'Receive Voucher Edit'=>'',
    ]
    @endphp
    <x-breadcrumb title='Receive Voucher' :links="$links"/>

    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Receive Voucher Details</h4>
                            <div class="text-right">
                                {{-- <a href="{{ route('receive-voucher.pdf', encrypt($receiveVoucher->id)) }}" class="btn btn-primary" target="_blank"><i class="fa fa-download"></i> PDF</a> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong>Date :</strong></th>
                                        <td>{{ $receiveVoucher->date }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>RV No :</strong></th>
                                        <td>{{ $receiveVoucher->uid }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Credit Account  :</strong></th>
                                        <td>{{ $receiveVoucher->creditAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Debit Account  :</strong></th>
                                        <td>{{ $receiveVoucher->debitAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Amount :</strong></th>
                                        <td>{{ $receiveVoucher->amount }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Receiver Name :</strong></th>
                                        <td>{{ $receiveVoucher->payee_name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Description :</strong></th>
                                        <td>{{ $receiveVoucher->narration }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Referance :</strong></th>
                                        <td>{{ $receiveVoucher->reference_no }}</td>
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
