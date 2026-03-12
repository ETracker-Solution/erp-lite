<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Total RM Consumption Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .report-title { font-size: 18px; font-weight: bold; }
        .report-date { font-size: 14px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-size: 12px; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #e6e6e6; }
        .spacer { border: none; height: 5px; }
    </style>
</head>
<body>
<div class="report-header">
    <div class="report-title">Total RM Consumption Report</div>
    <div class="report-date">Store: {{ $store ?? '' }}</div>
    <div class="report-date">{{ Carbon\Carbon::parse($from)->format('d M Y') }} To {{ Carbon\Carbon::parse($to)->format('d M Y') }}</div>
</div>

@if(!empty($rmGroupSummary))
<table>
    <thead>
    <tr>
        <th width="30%">RM Group</th>
        <th width="40%">RM Item</th>
        <th width="10%">Unit</th>
        <th width="20%" class="text-right">Total Quantity</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rmGroupSummary as $groupName => $groupData)
        @php $firstItem = true; @endphp
        @foreach($groupData['items'] as $itemName => $itemData)
            <tr>
                <td>{{ $firstItem ? $groupName : '' }}</td>
                <td>{{ $itemName }}</td>
                <td>{{ $itemData['unit'] }}</td>
                <td class="text-right">{{ number_format($itemData['total_quantity'], 4) }}</td>
            </tr>
            @php $firstItem = false; @endphp
        @endforeach
        <tr class="total-row">
            <td colspan="4" class="text-right">
                Group Total Items: {{ $groupData['total_items'] }}
            </td>
        </tr>
        <tr><td colspan="4" class="spacer"></td></tr>
    @endforeach
    </tbody>
</table>
@else
    <div style="text-align: center;">No data found for the selected criteria.</div>
@endif
</body>
</html>
