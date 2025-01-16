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
            <th>Requisition no.</th>
            <th>Outlet</th>
            <th>Group name</th>
            <th>Item name</th>
            <th>Quantity</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $chunks = $requisitions->chunk(100);
            // dd($chunks);
        @endphp
        @foreach ($chunks as $chunk)
            @foreach ($chunk as $item)
                <tr>
                    <td>{{ $item->requisition->date }}</td>
                    <td>{{ $item->requisition->uid }}</td>
                    <td>{{ $item->requisition->outlet->name }}</td>
                    <td>{{ $item->coi->parent->name }}</td>
                    <td>{{ $item->coi->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ ucfirst($item->requisition->status) }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
