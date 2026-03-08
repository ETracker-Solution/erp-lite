<table>
    <thead>
    <tr>
        <th>SL</th>
        <th>Invoice No</th>
        <th>Date</th>
        <th>Outlet</th>
        <th>Delivery Point</th>
        <th>Customer</th>
        <th>Subtotal</th>
        <th>Discount</th>
        <th>Grand Total</th>
        <th>Payment Method</th>
        <th>Items</th>
        <th>Groups</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sales as $key => $sale)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $sale->invoice_number }}</td>
            <td>{{ $sale->created_at->format('Y-m-d') }}</td>
            <td>{{ $sale->outlet->name ?? 'N/A' }}</td>
            <td>{{ $sale->deliveryPoint->name ?? 'N/A' }}</td>
            <td>{{ $sale->customer->name ?? 'N/A' }}</td>
            <td>{{ number_format($sale->subtotal, 2) }}</td>
            <td>{{ number_format($sale->discount, 2) }}</td>
            <td>{{ number_format($sale->grand_total, 2) }}</td>
            <td>
                @foreach($sale->payments as $payment)
                    {{ ucfirst($payment->payment_method) }} ({{ number_format($payment->amount, 2) }}){{ !$loop->last ? ', ' : '' }}
                @endforeach
            </td>
            <td>
                @foreach($sale->items as $item)
                    {{ $item->coi->name ?? 'N/A' }} ({{ $item->quantity }}){{ !$loop->last ? '; ' : '' }}
                @endforeach
            </td>
            <td>
                @php
                    $groups = $sale->items->map(function($item) {
                        return $item->coi->parent->name ?? 'N/A';
                    })->unique();
                @endphp
                @foreach($groups as $group)
                    {{ $group }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </td>
            <td>{{ ucfirst($sale->status) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
