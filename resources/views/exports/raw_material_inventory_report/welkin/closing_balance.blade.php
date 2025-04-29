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
            font-size: 12px
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
{{--    <p>1-Jul-24 to 27-Jan-25</p>--}}
</div>

<table>
    <thead>
    <tr style="border-top: 1px solid black">
        <th>Particulars</th>
        <th colspan="4" style="text-align: center">Closing Balance</th>
    </tr>
    <tr style="border-bottom: 1px solid black">
        <td></td>
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
            $hasVisibleItems = false;

            // First pass to check if we have any items that match our stock_type filter
            foreach($product->subChartOfInventories as $item) {
                $current_stock = $item->current_stock ?? 0;

                if(request('stock_type') == 'zero' && $current_stock == 0) {
                    $hasVisibleItems = true;
                    break;
                } elseif(request('stock_type') == 'non_zero' && $current_stock != 0) {
                    $hasVisibleItems = true;
                    break;
                } elseif(!request('stock_type') || request('stock_type') == '') {
                    $hasVisibleItems = true;
                    break;
                }
            }
        @endphp

        @if($hasVisibleItems)
            <tr class="category">
                <td colspan="5">{{ $product->name }}</td>
            </tr>

            @foreach($product->subChartOfInventories as $item)
                @php
                    $current_stock = $item->current_stock ?? 0;
                    $showItem = true;

                    // Apply stock type filter
                    if(request('stock_type') == 'zero' && $current_stock != 0) {
                        $showItem = false;
                    } elseif(request('stock_type') == 'non_zero' && $current_stock == 0) {
                        $showItem = false;
                    }
                @endphp

                @if($showItem)
                    @php
                        $value = $item->rate * $current_stock;
                        $parentSum += $value;
                    @endphp

                    <tr>
                        <td>{{$item->name}}</td>
                        <td>{{ $current_stock }} {{ $item->unit_id ? "(" . $item->unit->name . ')' : '' }}</td>
                        <td>
                            @if($item->alter_unit_id && $item->a_unit_quantity)
                                {{ round(($current_stock / $item->a_unit_quantity), 2) . '(' . $item->alterUnit->name . ')' }}
                            @endif
                        </td>
                        <td>{{ $current_stock ? ($item->rate ?? 0) : 0 }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endif
            @endforeach

            <tr>
                <td style="font-weight: 700; font-size:14px">Total of {{ $product->name }}</td>
                <td colspan="4"
                    style="text-align: right; font-weight: 700; border-top: 1px solid black; border-bottom: 1px solid black">
                    {{ $parentSum }}
                </td>
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
</body>
</html>
