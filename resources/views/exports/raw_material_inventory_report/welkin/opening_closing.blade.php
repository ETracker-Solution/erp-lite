<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 20px;
            margin: 5px 0;
        }

        .header p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        .footer {
            font-weight: bold;
            text-align: right;
            padding-right: 10px;
        }

        .category {
            font-weight: bold;
            background-color: #ddd;
        }

        .sub-footer {
            font-size: 10px;
            text-align: right;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Welkin Pastry Ltd.</h1>
    <p>1182/A Nurani Para, East Monipur,<br>Mirpur-2, Dhaka</p>
    <p>Email: welkinpastry@gmail.com</p>
    <h4>{{ $store->name ?? '' }}</h4>
    <h2>Stock Summary</h2>
    @if(request()->has('from_date') && request()->has('end_date'))
        <p>{{ \Carbon\Carbon::parse(request()->from_date)->format('d-M-y') }} to {{ \Carbon\Carbon::parse(request()->end_date)->format('d-M-y') }}</p>
    @endif
</div>

<table>
    <thead>
    <tr style="border-top: 1px solid black">
        <th style="border-right: 1px solid black">Particulars</th>
        <th colspan="4" style="text-align: center; border-right: 1px solid black">Opening Balance</th>
        <th colspan="4">Closing Balance</th>
    </tr>
    <tr style="border-bottom: 1px solid black">
        <td style="border-right: 1px solid black"></td>
        <td>Quantity</td>
        <td>(Alt Units)</td>
        <td>Rate</td>
        <td style="border-right: 1px solid black">Value</td>
        <td>Quantity</td>
        <td>(Alt Units)</td>
        <td>Rate</td>
        <td>Value</td>
    </tr>
    </thead>
    <tbody>

    @foreach($products as $product)
        @php
    $parentSum = 0;
        @endphp
        <tr class="category">
            <td colspan="17">{{ $product->name }}</td>
        </tr>
        @foreach($product->subChartOfInventories as $item)
            @php
        $opening_value = $item->opening_rate * $item->opening_stock;
        $closing_value = $item->closing_rate * $item->closing_stock;
            @endphp
            <tr>
                <td style="border-right: 1px solid black">{{$item->name}}</td>
                <td>{{ $item->opening_stock ?? ''}} {{ $item->opening_stock > 0 && $item->unit_id ? "(" . $item->unit->name . ')' : '' }}</td>
                <td>{{ $item->alter_unit_id && $item->a_unit_quantity > 0 && $item->opening_stock > 0 ? round(($item->opening_stock / $item->a_unit_quantity), 2) . '(' . $item->alterUnit->name . ')' : '' }}</td>
                <td>{{ $item->opening_stock > 0 ? $item->opening_rate ?? '' : ''}}</td>
                <td style="border-right: 1px solid black">{{ $opening_value > 0 ? $opening_value : '' }}</td>

                <td>{{ $item->closing_stock ?? ''}} {{ $item->closing_stock > 0 && $item->unit_id ? "(" . $item->unit->name . ')' : '' }}</td>
                <td>{{ $item->alter_unit_id && $item->a_unit_quantity > 0 && $item->closing_stock > 0 ? round(($item->closing_stock / $item->a_unit_quantity), 2) . '(' . $item->alterUnit->name . ')' : '' }}</td>
                <td>{{ $item->closing_stock > 0 ? $item->closing_rate ?? '' : ''}}</td>
                <td style="border-right: 1px solid black">{{ $closing_value > 0 ? $closing_value : '' }}</td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
</body>
</html>
