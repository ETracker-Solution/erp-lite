@extends('layouts.app')
@section('title', 'Supplier Ledger')
@section('content')
    @php
        $links = [
            'Home'=>route('dashboard'),
            'Accounts Module'=>'',
            'Supplier Due List'=>route('supplier-dues.index'),
            'Supplier Ledger'=>'',
        ]
    @endphp
    <x-breadcrumb title='Supplier Ledger : {{ $supplier->name }}' :links="$links"/>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Supplier Ledger: {{ $supplier->name }} ({{ $supplier->mobile }})</h3>
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
                                            // Supplier Transaction:
                                            // transaction_type: 1 = Purchase (Liability Increase) -> Credit side of Ledger?
                                            // Wait. Supplier Ledger usually: Credit (Purchase), Debit (Payment).
                                            // Balance = Credit - Debit.
                                            // Let's check schema: 1 = purchase, -1 = payment.
                                            
                                            // If 1 (Purchase), we owe them more. Is that Debit or Credit?
                                            // In Payable account: Purchase is Credit. Payment is Debit.
                                            // Only if we view it as "Our Liability".
                                            // Let's assume standard Payable Ledger:
                                            // Credit (Purchase in), Debit (Payment out).
                                            
                                            $credit = $transaction->transaction_type == 1 ? $transaction->amount : 0;
                                            $debit = $transaction->transaction_type == -1 ? $transaction->amount : 0;
                                            $balance += ($credit - $debit); // Liability increases with Credit
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
