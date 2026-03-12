<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Without Pre-Order Wise Consumption Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .report-title { font-size: 18px; font-weight: bold; }
        .report-date { font-size: 14px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-size: 12px; }
        .group-row { background-color: #e6f3ff; font-weight: bold; }
        .fg-row { background-color: #f9f9f9; font-weight: bold; }
        .rm-row { font-size: 0.9em; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #e6e6e6; }
        .spacer { border: none; height: 10px; }
    </style>
</head>
<body>
<div class="report-header">
    <div class="report-title">Without Pre-Order Wise Consumption Report</div>
    <div class="report-date">Store: {{ $store ?? '' }}</div>
    <div class="report-date">{{ Carbon\Carbon::parse($from)->format('d M Y') }} To {{ Carbon\Carbon::parse($to)->format('d M Y') }}</div>
</div>

<table>
    <thead>
    <tr>
        <th width="5%">SI</th>
        <th width="20%">Group Name</th>
        <th width="25%">Item Name</th>
        <th width="10%" class="text-right">FG Qty</th>
        <th width="25%">RM Item</th>
        <th width="10%">Unit</th>
        <th width="15%" class="text-right">RM Qty</th>
    </tr>
    </thead>
    <tbody>
    @php $siNo = 1; @endphp
    @foreach ($processedData as $groupName => $fgItems)
        @php $groupFirst = true; @endphp
        @foreach ($fgItems as $fgName => $fgData)
            <tr class="fg-row">
                <td>{{ $siNo++ }}</td>
                <td>{{ $groupFirst ? $groupName : '' }}</td>
                <td>{{ $fgName }}</td>
                <td class="text-right">{{ number_format($fgData['quantity'], 2) }}</td>
                <td colspan="3" style="background-color: #fff;"></td>
            </tr>

            @foreach ($fgData['rm_items'] as $rmName => $rmData)
                <tr class="rm-row">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $rmName }}</td>
                    <td>{{ $rmData['unit'] }}</td>
                    <td class="text-right">{{ number_format($rmData['total_quantity'], 4) }}</td>
                </tr>
            @endforeach
            @php $groupFirst = false; @endphp
        @endforeach

        <tr class="total-row">
            <td colspan="7" class="text-right">
                Group Total: {{ number_format(array_sum(array_column($fgItems, 'quantity')), 2) }} items
            </td>
        </tr>
        <tr><td colspan="7" class="spacer"></td></tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
