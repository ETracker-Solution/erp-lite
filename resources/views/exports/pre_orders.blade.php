<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    th {
        background-color: #c4c7c7;
    }

    .headers {
        text-align: center
    }

    .signature-section {
        margin-top: 50px;
        display: flex;
        justify-content: space-between;
    }

    .signature-section div {
        text-align: center;
        font-weight: bold;
        width: 32%;
    }
</style>
<table style="width: 100%">
    <thead>
        <tr>
            <th>Date</th>
            <th>Invoice no.</th>
            <th>Outlet</th>
            <th>Group name</th>
            <th>Item name</th>
            <th>Quantity/Weight</th>
            <th>Price</th>
            <th>Total value</th>
            <th>Discount</th>
            <th>Value after Discount</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $chunks = $preOrderItems->chunk(100);
            // dd($chunks);
        @endphp
        @foreach ($chunks as $chunk)
            @foreach ($chunk as $item)
            @php
                $total_value = $item->unit_price * $item->quantity;
            @endphp
                <tr>
                    <td>{{ $item->preOrder->order_date }}</td>
                    <td>{{ $item->preOrder->order_number }}</td>
                    <td>{{ $item->preOrder->outlet->name }}</td>
                    <td>{{ $item->coi->parent->name }}</td>
                    <td>{{ $item->coi->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($total_value, 2) }}</td>
                    <td>{{ number_format($item->discount, 2) }}</td>
                    <td>{{ number_format($total_value - $item->discount, 2) }}</td>
                    <td>{{ ucfirst($item->preOrder->status) }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
