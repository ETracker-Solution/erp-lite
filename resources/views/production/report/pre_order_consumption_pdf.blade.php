<?php

$processedData = [];
$totalOverall = 0;

foreach ($preOrderConsumption as $transaction) {
    $fgItem = $transaction->chartOfInventory;
    $fgName = $fgItem->name;
    $groupName = $fgItem->parent->name ?? 'Uncategorized';
    $quantity = $transaction->quantity;

    if (!isset($processedData[$groupName])) {
        $processedData[$groupName] = [];
    }

    if (!isset($processedData[$groupName][$fgName])) {
        $processedData[$groupName][$fgName] = [
            'quantity' => 0,
            'rm_items' => []
        ];
    }

    $processedData[$groupName][$fgName]['quantity'] += $quantity;

    // ✅ FIX: Fallback to $fgItem->recipes if productionRecipes is empty
    $recipes = $fgItem->productionRecipes;
    if (empty($recipes) || $recipes->isEmpty()) {
        $recipes = $fgItem->recipes ?? collect();
    }

    foreach ($recipes as $recipe) {
        $rmItem = $recipe->coi;
        $rmName = $rmItem->name;
        $unit = $rmItem->unit->name ?? '';
        $rmQuantity = $recipe->qty * $quantity;
        $stockCost = $rmItem->price ?? 0;

        if (!isset($processedData[$groupName][$fgName]['rm_items'][$rmName])) {
            $processedData[$groupName][$fgName]['rm_items'][$rmName] = [
                'unit' => $unit,
                'stock_cost' => $stockCost,
                'total_quantity' => 0
            ];
        }

        $processedData[$groupName][$fgName]['rm_items'][$rmName]['total_quantity'] += $rmQuantity;
    }
}

ksort($processedData);
?>

    <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #2d3748;
            background: #fff;
            padding: 24px;
        }

        /* ── Header ── */
        .report-header {
            text-align: center;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 3px solid #2b6cb0;
        }
        .report-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a365d;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .report-date {
            font-size: 12px;
            color: #718096;
        }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            table-layout: fixed;
        }

        /* Column widths */
        col.col-si   { width: 5%; }
        col.col-grp  { width: 16%; }
        col.col-item { width: 22%; }
        col.col-qty  { width: 9%; }
        col.col-rm   { width: 25%; }
        col.col-unit { width: 12%; }
        col.col-cost { width: 11%; }

        thead tr:first-child th {
            background: #1a365d;
            color: #fff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 9px 8px;
            border: 1px solid #2c5282;
            vertical-align: middle;
        }
        thead tr:last-child th {
            background: #2b6cb0;
            color: #ebf8ff;
            font-size: 10px;
            padding: 6px 8px;
            border: 1px solid #2c5282;
        }

        tbody td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }

        /* Group header row */
        .group-header td {
            background: #ebf8ff;
            font-weight: 700;
            color: #1a365d;
            font-size: 11.5px;
            padding: 7px 8px;
            border-top: 2px solid #2b6cb0;
            border-bottom: 1px solid #bee3f8;
        }

        /* FG item rows */
        .fg-row td {
            background: #f7fafc;
        }
        .fg-row td:nth-child(3) {
            font-weight: 600;
            color: #2d3748;
        }

        /* RM rows */
        .rm-row td {
            background: #fff;
            color: #4a5568;
            font-size: 11px;
        }
        .rm-row td:nth-child(5) {
            padding-left: 20px;
            color: #2d3748;
        }

        /* Divider between FG items within a group */
        .fg-divider td {
            padding: 0;
            height: 4px;
            background: #edf2f7;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            border-top: none;
            border-bottom: none;
        }

        /* Group total row */
        .group-total td {
            background: #2b6cb0;
            color: #fff;
            font-weight: 700;
            font-size: 11px;
            padding: 6px 8px;
            border: 1px solid #2c5282;
            text-align: right;
        }

        /* Gap row between groups */
        .group-gap td {
            border: none;
            padding: 6px 0;
            background: #fff;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .muted { color: #a0aec0; }
    </style>
</head>
<body>

<div class="report-header">
    <div class="report-title">Pre-Order Wise Consumption Report</div>
    <div class="report-date">
        {{ Carbon\Carbon::parse($from)->format('d M Y') }} &mdash; {{ Carbon\Carbon::parse($to)->format('d M Y') }}
    </div>
</div>

<table>
    <colgroup>
        <col class="col-si">
        <col class="col-grp">
        <col class="col-item">
        <col class="col-qty">
        <col class="col-rm">
        <col class="col-unit">
        <col class="col-cost">
    </colgroup>
    <thead>
    <tr>
        <th class="text-center">SI No</th>
        <th>Group Name</th>
        <th>Item Name</th>
        <th class="text-right">Qty</th>
        <th colspan="3" class="text-center">RM Consumption</th>
    </tr>
    <tr>
        <th></th><th></th><th></th><th></th>
        <th>Raw Material</th>
        <th>Unit</th>
        <th class="text-right">Quantity</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $siNo = 1;

    foreach ($processedData as $groupName => $fgItems):
        $groupItemCount = count($fgItems);
        $itemIndex = 0;
        $groupTotal = array_sum(array_column($fgItems, 'quantity'));
        ?>

        <!-- Group Header -->
    <tr class="group-header">
        <td class="text-center muted">&mdash;</td>
        <td colspan="6"><?php echo htmlspecialchars($groupName); ?></td>
    </tr>

        <?php foreach ($fgItems as $fgName => $fgData):
        $itemIndex++;
        $isLastItem = ($itemIndex === $groupItemCount);
        ?>

        <!-- FG Item Row -->
    <tr class="fg-row">
        <td class="text-center"><?php echo $siNo++; ?></td>
        <td></td>
        <td><?php echo htmlspecialchars($fgName); ?></td>
        <td class="text-right"><?php echo number_format($fgData['quantity'], 2); ?></td>
        <td colspan="3"></td>
    </tr>

    <!-- RM Rows -->
        <?php if (!empty($fgData['rm_items'])): ?>
        <?php foreach ($fgData['rm_items'] as $rmName => $rmData): ?>
    <tr class="rm-row">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><?php echo htmlspecialchars($rmName); ?></td>
        <td><?php echo htmlspecialchars($rmData['unit']); ?></td>
        <td class="text-right"><?php echo number_format($rmData['total_quantity'], 4); ?></td>
    </tr>
    <?php endforeach; ?>
    <?php else: ?>
    <tr class="rm-row">
        <td></td><td></td><td></td><td></td>
        <td colspan="3" class="muted" style="font-style:italic;">No RM data available</td>
    </tr>
    <?php endif; ?>

        <!-- Divider between FG items (not after last) -->
        <?php if (!$isLastItem): ?>
    <tr class="fg-divider"><td colspan="7"></td></tr>
    <?php endif; ?>

    <?php endforeach; ?>

        <!-- Group Total -->
    <tr class="group-total">
        <td colspan="7">
            Group Total &mdash; <?php echo htmlspecialchars($groupName); ?>:
                <?php echo number_format($groupTotal, 2); ?> items
        </td>
    </tr>

    <!-- Gap between groups -->
    <tr class="group-gap"><td colspan="7"></td></tr>

    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
