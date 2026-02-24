@php
    use Carbon\Carbon;

    $rmGroupSummary = [];

    foreach ($totalConsumption as $transaction) {

        $item = $transaction->chartOfInventory;

        if (!$item) continue;

        $groupName = $item->parent->name ?? 'Uncategorized';
        $itemName  = $item->name;
        $unit      = $item->unit->name ?? '';
        $qty       = $transaction->quantity ?? 0;

        if (!isset($rmGroupSummary[$groupName])) {
            $rmGroupSummary[$groupName] = [
                'total_items' => 0,
                'items' => []
            ];
        }

        if (!isset($rmGroupSummary[$groupName]['items'][$itemName])) {
            $rmGroupSummary[$groupName]['items'][$itemName] = [
                'total_quantity' => 0,
                'unit' => $unit
            ];
        }

        $rmGroupSummary[$groupName]['items'][$itemName]['total_quantity'] += $qty;
        $rmGroupSummary[$groupName]['total_items']++;
    }

    ksort($rmGroupSummary);
@endphp


    <!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .report-title { font-size: 18px; font-weight: bold; }
        .report-date { font-size: 14px; margin-bottom: 20px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }

        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #e6e6e6; }
    </style>
</head>
<body>

<div class="report-header">
    <div class="report-title">Total RM Consumption Report</div>
    <div class="report-date">Store: {{ $store ?? '' }}</div>

    <div class="report-date">
        {{ Carbon::parse($from)->format('d M Y') }}
        To
        {{ Carbon::parse($to)->format('d M Y') }}
    </div>
</div>

@if(!empty($rmGroupSummary))

    <table>
        <thead>
        <tr>
            <th>RM Group</th>
            <th>RM Item</th>
            <th>Unit</th>
            <th>Total Quantity</th>
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
                    <td class="text-right">
                        {{ number_format($itemData['total_quantity'], 4) }}
                    </td>
                </tr>

                @php $firstItem = false; @endphp

            @endforeach

            <tr class="total-row">
                <td colspan="4" style="text-align: right;">
                    Group Total Items: {{ $groupData['total_items'] }}
                </td>
            </tr>

            <tr>
                <td colspan="4" style="border:none;height:5px;"></td>
            </tr>

        @endforeach

        </tbody>
    </table>

@endif

</body>
</html>
