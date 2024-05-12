<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        h4 {
            margin: 0;
        }

        .w-full {
            width: 100%;
        }

        .w-half {
            width: 50%;
        }

        .margin-top {
            margin-top: 1.25rem;
        }

        .footer {
            font-size: 0.875rem;
            padding: 1rem;
            background-color: rgb(255, 255, 255);
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        table.products {
            font-size: 0.875rem;
            border: 1px solid black;
        }

        table.products tr {
            background-color: rgb(0, 43, 96);
            border: 1px solid black;
        }

        table.products th {
            color: #ffffff;
            padding: 0.5rem;
            border: 1px solid black;
        }

        table tr.items {
            background-color: rgb(243, 248, 253);
        }

        table tr.items td {
            padding: 0.5rem;
            border: 1px solid black;
        }

        .total {
            text-align: right;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
        .headers{
            text-align: center
        }

    </style>
    <title>Invoice</title>
</head>
<body>
<table class="w-full">
    <tr>
        <td class="w-full headers">
            <h3>RM Inventory Report</h3>
            <span>{{ $dateRange }}</span>
        </td>
    </tr>
</table>
<table  class="w-full">
    <tr>
        <td>@if($one_title) {{ ucfirst($one_value).' Name: '.ucwords($data[0][$one_value]) }} @endif</td>
    </tr>
</table>
{{--<div class="margin-top">--}}
{{--    <table class="w-full">--}}
{{--        <tr>--}}
{{--            <td class="w-half">--}}
{{--                <div><h4>To:</h4></div>--}}
{{--                <div>John Doe</div>--}}
{{--                <div>123 Acme Str.</div>--}}
{{--            </td>--}}
{{--            <td class="w-half">--}}
{{--                <div><h4>From:</h4></div>--}}
{{--                <div>Laravel Daily</div>--}}
{{--                <div>London</div>--}}
{{--            </td>--}}
{{--        </tr>--}}
{{--    </table>--}}
{{--</div>--}}
@php
    $group = null;
    $store = null;
@endphp
<div class="margin-top">
    <table class="products">
        <tr>
            @if($visible_columns['store'])
                <th>Store</th>
            @endif
            @if($visible_columns['group'])
                <th>Group</th>
            @endif
            @if($visible_columns['item'])
                <th>Item</th>
            @endif
            @if($visible_columns['quantity'])
                <th>Quantity</th>
            @endif
                <th>Balance</th>
        </tr>
        @foreach($data as $key=>$item)
            <tr class="items">
                @if($visible_columns['store'])
                    <td>{{ $store !== $item->store ? $item->store: '' }}</td>
                @endif
                @if($visible_columns['group'])
                        <td>{{ $group !== $item->group ? $item->group : '' }}</td>
                @endif
                @if($visible_columns['item'])
                    <td>  {{ $item->item }}</td>
                @endif
                @if($visible_columns['quantity'])
                    <td>  {{ $item->total }}</td>
                @endif
                    <td>{{ $item->TAmount }}</td>
            </tr>
            @php
                $store = $item->store;
                $group = $item->group;
            @endphp
        @endforeach
    </table>
</div>
{{--<div class="total">--}}
{{--    Total: $129.00 USD--}}
{{--</div>--}}

{{--<div class="footer margin-top">--}}
{{--    <div>Thank you</div>--}}
{{--    <div>&copy; Laravel Daily</div>--}}
{{--</div>--}}
</body>
</html>
