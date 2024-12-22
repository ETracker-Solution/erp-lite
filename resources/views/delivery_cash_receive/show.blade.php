@extends('layouts.app')

@section('title', 'Delivery Cash Transfer')
@section('content')
@push('style')
@endpush
    @php
    $links = [
    'Home'=>route('dashboard'),
    'Delivery Cash Transfer'=>route('fund-transfer-vouchers.index'),
    'Delivery Cash Transfer Details'=>'',
    ]
    @endphp
    <x-breadcrumb title='Delivery Cash Transfer' :links="$links"/>
    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Delivery Cash Transfer Details</h4>
                            <div class="text-right">
                                {{-- <a href="{{ route('fund-transfer-voucher.pdf', encrypt($fundTransferVoucher->id)) }}" class="btn btn-primary" target="_blank"><i class="fa fa-download"></i> PDF</a> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th><strong>Date :</strong></th>
                                        <td>{{ $deliveryCashTransfer->date }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Delivery Invoice :</strong></th>
                                        <td>{{ $deliveryCashTransfer->invoice_number }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Other Outlet :</strong></th>
                                        <td>{{ $deliveryCashTransfer->otherOutlet ? $deliveryCashTransfer->otherOutlet->outlet->name : '' }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Transfer From Account  :</strong></th>
                                        <td>{{ $deliveryCashTransfer->creditAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Transfer To Account  :</strong></th>
                                        <td>{{ $deliveryCashTransfer->debitAccount->name }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Amount :</strong></th>
                                        <td>{{ $deliveryCashTransfer->amount }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Description :</strong></th>
                                        <td>{{ $deliveryCashTransfer->narration }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Referance :</strong></th>
                                        <td>{{ $deliveryCashTransfer->reference_no }}</td>
                                    </tr>
                                    <tr>
                                        <th><strong>Status :</strong></th>
                                        <td>{{ strtoupper($deliveryCashTransfer->status) }}</td>
                                    </tr>

                                </thead>
                            </table>

                        </div>

                        {{-- adjust modal --}}

                    </div>
                    @if($deliveryCashTransfer->status == 'pending')
                    <form action="{{ route('delivery-cash-receives.update', encrypt($deliveryCashTransfer->id)) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button class="btn btn-success">Received</button>
                    </form>
                    @endif
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
