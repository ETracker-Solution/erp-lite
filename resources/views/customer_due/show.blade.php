@extends('layouts.app')
@section('title', 'Customer Ledger')
@section('content')
    @php
        $links = [
            'Home'=>route('dashboard'),
            'Accounts Module'=>'',
            'Customer Due List'=>route('customer-dues.index'),
            'Customer Ledger'=>'',
        ]
    @endphp
    <x-breadcrumb title='Customer Ledger : {{ $customer->name }}' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Customer Ledger: {{ $customer->name }} ({{ $customer->mobile }})</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php $balance = 0; @endphp
                                    @foreach($transactions as $transaction)
                                        @php
                                            // transaction_type: 1 = Sale (Debit), -1 = Recieve (Credit)
                                            // Debit increases balance (receivable), Credit decreases it.
                                            
                                            $debit = $transaction->transaction_type == 1 ? $transaction->amount : 0;
                                            $credit = $transaction->transaction_type == -1 ? $transaction->amount : 0;
                                            $balance += ($debit - $credit);
                                        @endphp
                                        <tr>
                                            <td>{{ $transaction->date }}</td>
                                            <td>{{ $transaction->description ?? 'N/A' }} 
                                                @if($transaction->doc_type && $transaction->doc_id)
                                                 ({{ $transaction->doc_type }} #{{ $transaction->doc_id }})
                                                @endif
                                            </td>
                                            <td class="text-right">{{ $debit > 0 ? number_format($debit, 2) : '-' }}</td>
                                            <td class="text-right">{{ $credit > 0 ? number_format($credit, 2) : '-' }}</td>
                                            <td class="text-right">{{ number_format($balance, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Closing Balance</th>
                                        <th class="text-right">{{ number_format($balance, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
