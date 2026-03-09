<table>
    <thead>
    <tr>
        <th>SL</th>
        <th>Date</th>
        <th>FTV No</th>
        <th>Transfer From</th>
        <th>Transfer To</th>
        <th>Amount</th>
        <th>Narration</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($vouchers as $key => $voucher)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $voucher->date }}</td>
            <td>{{ $voucher->uid }}</td>
            <td>{{ $voucher->creditAccount->name ?? 'N/A' }}</td>
            <td>{{ $voucher->debitAccount->name ?? 'N/A' }}</td>
            <td>{{ number_format($voucher->amount, 2) }}</td>
            <td>{{ $voucher->narration }}</td>
            <td>{{ ucfirst($voucher->status) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
