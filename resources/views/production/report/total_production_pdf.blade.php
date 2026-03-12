<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Total Production Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .report-title { font-size: 18px; font-weight: bold; }
        .report-date { font-size: 14px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-size: 12px; }
        .group-row { background-color: #e6f3ff; font-weight: bold; }
        .fg-row { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #e6e6e6; }
        .grand-total { font-weight: bold; background-color: #d9ead3; }
        .spacer { border: none; height: 5px; background-color: transparent; }
    </style>
</head>
<body>
<div class="report-header">
    <div class="report-title">Total Production Report</div>
    <div class="report-date">Store: {{ $store ?? '' }}</div>
    <div class="report-date">{{ Carbon\Carbon::parse($from)->format('d M Y') }} To {{ Carbon\Carbon::parse($to)->format('d M Y') }}</div>
</div>

<table>
    <thead>
    <tr>
        <th width="10%">SI No</th>
        <th width="30%">Group Name</th>
        <th width="40%">Item Name</th>
        <th width="20%" class="text-right">Qty</th>
    </tr>
    </thead>
    <tbody>
    @php
        $siNo = 1;
        $grandTotal = 0;
    @endphp

    @foreach ($processedData as $groupName => $fgItems)
        @php
            $groupTotal = array_sum(array_column($fgItems, 'quantity'));
            $groupFirst = true;
        @endphp
        
        @foreach ($fgItems as $fgName => $fgData)
            <tr class="fg-row">
                <td>{{ $groupFirst ? $siNo++ : '' }}</td>
                <td>{{ $groupFirst ? $groupName : '' }}</td>
                <td>{{ $fgName }}</td>
                <td class="text-right">{{ number_format($fgData['quantity'], 2) }}</td>
            </tr>
            @php
                $grandTotal += $fgData['quantity'];
                $groupFirst = false;
            @endphp
        @endforeach
        
        <tr class="total-row">
            <td colspan="3" class="text-right">Group Total</td>
            <td class="text-right">{{ number_format($groupTotal, 2) }}</td>
        </tr>
        <tr><td colspan="4" class="spacer"></td></tr>
    @endforeach

    <tr class="grand-total">
        <td colspan="3" class="text-right">Grand Total</td>
        <td class="text-right">{{ number_format($grandTotal, 2) }}</td>
    </tr>
    </tbody>
</table>
</body>
</html>
