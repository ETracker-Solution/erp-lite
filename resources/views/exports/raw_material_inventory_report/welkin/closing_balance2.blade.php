<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .table-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-bottom: 2px solid #000;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
<div class="table-header">
    <table class="table">
        <thead>
        <tr>
            <th>Particulars</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Value</th>
        </tr>
        </thead>
    </table>
</div>
<div class="content">
    <table class="table">
        <thead>
        <tr>
            <th>Particulars</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Value</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
            @php
                $parentSum = 0;
            @endphp
            <tr>
                <td colspan="4">{{ $product->name }}</td>
            </tr>
            @foreach($product->subChartOfInventories as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td style="text-align: right">{{ $item->current_stock ?? 0 }}</td>
                    <td style="text-align: right">{{ ($item->rate) * ($item->current_stock) }}2</td>
                    <td style="text-align: right">{{ $item->value ?? 0 }}</td>
                </tr>
                @php
                    $parentSum += $item->value ?? 0;
                @endphp
            @endforeach
            <tr>
                <td colspan="4" style="text-align: right; font-weight: 700">{{ $parentSum }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
{{--    Page: <script>document.write('{{ $page }}');</script>--}}
</div>
</body>
</html>
