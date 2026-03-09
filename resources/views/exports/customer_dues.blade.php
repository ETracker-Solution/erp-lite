<table>
    <thead>
    <tr>
        <th>Customer Name</th>
        <th>Phone</th>
        <th>Date</th>
        <th>Description</th>
        <th>Debit</th>
        <th>Credit</th>
        <th>Balance</th>
    </tr>
    </thead>
    <tbody>
    @foreach($customers as $customer)
        @php
            $balance = 0;
        @endphp
        <tr>
            <td style="font-weight: bold; background-color: #f2f2f2;">{{ $customer->name }}</td>
            <td style="font-weight: bold; background-color: #f2f2f2;">{{ $customer->mobile }}</td>
            <td colspan="4" style="font-weight: bold; background-color: #f2f2f2;">Opening Balance/Summary</td>
            <td style="font-weight: bold; background-color: #f2f2f2; text-align: right;">{{ number_format($customer->due_amount, 2) }}</td>
        </tr>
        @foreach($customer->customerTransactions as $transaction)
            @php
                $debit = $transaction->transaction_type == 1 ? $transaction->amount : 0;
                $credit = $transaction->transaction_type == -1 ? $transaction->amount : 0;
                $balance += ($debit - $credit);
            @endphp
            <tr>
                <td></td>
                <td></td>
                <td>{{ $transaction->date }}</td>
                <td>{{ $transaction->description }}</td>
                <td style="text-align: right;">{{ $debit > 0 ? number_format($debit, 2) : '' }}</td>
                <td style="text-align: right;">{{ $credit > 0 ? number_format($credit, 2) : '' }}</td>
                <td style="text-align: right;">{{ number_format($balance, 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="7"></td>
        </tr>
    @endforeach
    </tbody>
</table>
