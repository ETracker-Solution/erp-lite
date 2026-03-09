@extends('layouts.app')

@section('title', 'Receive Voucher')
@section('content')
    @push('style')
    @endpush
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Accounts Module'=>'',
    'General Accounts'=>'',
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
                            <div class="card-tools">
                                <a href="{{route('receive-vouchers.index')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </button>
                                </a>
                                <a href="{{ route('receive-voucher.pdf', encrypt($voucher->id)) }}" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-download"></i> PDF</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong>Date :</strong></th>
                                        <td>{{ $voucher->date }}</td>
                                        <th><strong>RV No :</strong></th>
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
                                        <th>Income Head</th>
                                        <th>Payee Name</th>
                                        <th>Reference No</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach($receiveVouchers as $index => $item)
                                    @php $total += $item->amount; @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->debitAccount->name ?? '' }}</td>
                                        <td>{{ $item->creditAccount->name ?? '' }}</td>
                                        <td>{{ $item->payee_name }}</td>
                                        <td>{{ $item->reference_no }}</td>
                                        <td>{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total:</th>
                                        <th>{{ number_format($total, 2) }}</th>
                                    </tr>
                                </tfoot>
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
