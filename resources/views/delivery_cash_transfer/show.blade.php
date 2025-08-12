@extends('layouts.app')

@section('title', 'Delivery Cash Transfer')
@section('content')
    @push('style')
    @endpush
    @php
        $links = [
        'Home'=>route('dashboard'),
        'Accounts Module'=>'',
        'General Accounts'=>'',
        'Delivery Cash Transfer Details'=>'',
        ]
    @endphp
    <x-breadcrumb title='Delivery Cash Transfer' :links="$links"/>
    <!-- Basic Inputs start -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h4 class="card-title">Delivery Cash Transfer Details</h4>
                            <div class="card-tools">
                                <a href="{{route('delivery-cash-transfers.index')}}">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-list" aria-hidden="true"></i>
                                        &nbsp;See List
                                    </button>
                                </a>
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
                                    <th><strong>Transfer From Account :</strong></th>
                                    <td>{{ $deliveryCashTransfer->creditAccount->name }}</td>
                                </tr>
                                <tr>
                                    <th><strong>Transfer To Account :</strong></th>
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
