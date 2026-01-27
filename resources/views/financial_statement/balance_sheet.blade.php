<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Sheet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .header h2 {
            margin: 10px 0 5px 0;
            font-size: 14px;
            font-weight: bold;
        }

        .date-range {
            font-size: 10px;
            margin: 5px 0 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border: 1px solid #000;
        }

        th {
            background-color: #fff;
            color: #000;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border-bottom: 1px solid #000;
        }

        th:first-child {
            border-right: 1px solid #000;
        }

        th:nth-child(3) {
            border-left: 1px solid #000;
        }

        td {
            padding: 5px 8px;
            vertical-align: top;
            border: none;
        }

        td:nth-child(2) {
            border-right: 1px solid #000;
        }

        td:nth-child(3) {
            border-left: 1px solid #000;
        }

        .amount {
            text-align: right;
        }

        .section-header {
            font-weight: bold;
            padding-left: 5px;
        }

        .sub-item {
            padding-left: 20px;
            font-style: italic;
        }

        .sub-sub-item {
            padding-left: 40px;
            font-size: 10px;
        }

        .section-subtotal {
            border-bottom: 1px solid #000;
        }

        .total-row {
            font-weight: bold;
            background-color: #fff;
            border-top: 1px solid #000 !important;
        }

        .total-row td {
            border-top: 1px solid #000;
        }

        .total-row td:nth-child(2) {
            border-right: 1px solid #000;
        }

        .total-row td:nth-child(3) {
            border-left: 1px solid #000;
        }

        /* Ensure proper column widths */
        .liability-col {
            width: 23%;
        }

        .liability-amount-col {
            width: 10%;
        }

        .asset-col {
            width: 23%;
        }

        .asset-amount-col {
            width: 10%;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Welkin Pastry Ltd.</h1>
    <p>1182/A Nurani Para, East Monipur,</p>
    <p>Mirpur-2, Dhaka</p>
    <p>E-Mail : welkinpastry@gmail.com</p>
    <h2>Balance Sheet</h2>
    @if(request()->has('asOnDate'))
        <p class="date-range">{{ \Carbon\Carbon::parse($asOnDate)->format('d-M-y') }} to {{ \Carbon\Carbon::parse($asOnDate)->format('d-M-y') }}</p>
    @else
{{--        <p class="date-range">1-Dec-25 to 31-Dec-25</p>--}}
    @endif
</div>

<table>
    <thead>
    <tr>
        <th class="liability-col">Liabilities</th>
        <th class="liability-amount-col amount"></th>
        <th class="asset-col">Assets</th>
        <th class="asset-amount-col amount"></th>
    </tr>
    </thead>
    <tbody>
    @php
        // Convert collections to arrays for easier iteration
        $liabilitiesArray = $liabilities->toArray();
        $assetsArray = $assets->toArray();
        $maxRows = max(count($liabilitiesArray), count($assetsArray));

        // Create flattened arrays with parent and children
        $liabilityRows = [];
        foreach ($liabilities as $liability) {
            $liabilityRows[] = [
                'name' => $liability->name,
                'amount' => number_format($liability->getTotalBalanceAttribute($asOnDate), 2),
                'type' => 'parent',
                'isLastChild' => false
            ];
            $childCount = count($liability->childrens);
            $childIndex = 0;
            foreach ($liability->childrens as $child) {
                $childIndex++;
                $liabilityRows[] = [
                    'name' => $child->name,
                    'amount' => number_format($child->getTotalBalanceAttribute($asOnDate), 2),
                    'type' => 'child',
                    'isLastChild' => ($childIndex === $childCount)
                ];
            }
        }

        // Create flattened arrays for assets with nested children
        $assetRows = [];
        foreach ($assets as $asset) {
            $assetRows[] = [
                'name' => $asset->name,
                'amount' => number_format($asset->getTotalBalanceAttribute($asOnDate), 2),
                'type' => 'parent',
                'isLastChild' => false
            ];
            $childCount = count($asset->childrens);
            $childIndex = 0;
            foreach ($asset->childrens as $child) {
                $childIndex++;
                $hasGrandchildren = count($child->childrens) > 0;

                if (!$hasGrandchildren) {
                    $assetRows[] = [
                        'name' => $child->name,
                        'amount' => number_format($child->getTotalBalanceAttribute($asOnDate), 2),
                        'type' => 'child',
                        'isLastChild' => ($childIndex === $childCount)
                    ];
                } else {
                    $assetRows[] = [
                        'name' => $child->name,
                        'amount' => number_format($child->getTotalBalanceAttribute($asOnDate), 2),
                        'type' => 'child',
                        'isLastChild' => false
                    ];

                    $grandchildCount = count($child->childrens);
                    $grandchildIndex = 0;
                    foreach ($child->childrens as $child2) {
                        $grandchildIndex++;
                        $assetRows[] = [
                            'name' => $child2->name,
                            'amount' => number_format($child2->getTotalBalanceAttribute($asOnDate), 2),
                            'type' => 'subchild',
                            'isLastChild' => ($grandchildIndex === $grandchildCount && $childIndex === $childCount)
                        ];
                    }
                }
            }
        }

        $maxDataRows = max(count($liabilityRows), count($assetRows));
    @endphp

    @for ($i = 0; $i < $maxDataRows; $i++)
        <tr>
            @if ($i < count($liabilityRows))
                <td class="{{ $liabilityRows[$i]['type'] === 'parent' ? 'section-header' : 'sub-item' }}">
                    {{ $liabilityRows[$i]['name'] }}
                </td>
                <td class="amount {{ $liabilityRows[$i]['isLastChild'] ? 'section-subtotal' : '' }}">{{ $liabilityRows[$i]['amount'] }}</td>
            @else
                <td></td>
                <td></td>
            @endif

            @if ($i < count($assetRows))
                <td class="{{ $assetRows[$i]['type'] === 'parent' ? 'section-header' : ($assetRows[$i]['type'] === 'child' ? 'sub-item' : 'sub-sub-item') }}">
                    {{ $assetRows[$i]['name'] }}
                </td>
                <td class="amount {{ $assetRows[$i]['isLastChild'] ? 'section-subtotal' : '' }}">{{ $assetRows[$i]['amount'] }}</td>
            @else
                <td></td>
                <td></td>
            @endif
        </tr>
    @endfor

    <!-- Total Row -->
    <tr class="total-row">
        <td>Total</td>
        <td class="amount">{{ number_format($totalLiability, 2) }}</td>
        <td>Total</td>
        <td class="amount">{{ number_format($totalAsset, 2) }}</td>
    </tr>
    </tbody>
</table>
</body>
</html>
