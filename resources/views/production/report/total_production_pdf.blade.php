<?php

$processedData = [];
$totalOverall = 0;

foreach ($totalProduction as $transaction) {
    $fgItem = $transaction->chartOfInventory;
    $fgName = $fgItem->name;
    $groupName = $fgItem->parent->name ?? 'Uncategorized';
    $quantity = $transaction->quantity;

    // Initialize group if not exists
    if (!isset($processedData[$groupName])) {
        $processedData[$groupName] = [];
    }

    // Initialize FG item if not exists in group
    if (!isset($processedData[$groupName][$fgName])) {
        $processedData[$groupName][$fgName] = [
            'quantity' => 0,
            'rm_items' => []
        ];
    }

    // Add quantity
    $processedData[$groupName][$fgName]['quantity'] += $quantity;
    $totalOverall += $quantity;
}

// Sort by group name
ksort($processedData);

// Sort items within each group alphabetically
foreach ($processedData as &$fgItems) {
    ksort($fgItems);
}

?>

    <!-- HTML/PHP for PDF Report -->
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
        th { background-color: #f2f2f2; }
        .group-row { background-color: #e6f3ff; font-weight: bold; }
        .fg-row { background-color: #f9f9f9; }
        .rm-row { padding-left: 40px; font-size: 0.9em; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #e6e6e6; }
        .grand-total { font-weight: bold; background-color: #d9ead3; }
    </style>
</head>
<body>
<div class="report-header">
    <div class="report-title">Total Production Report</div>
    <div class="report-date">{{ Carbon\Carbon::parse($from)->format('d M Y') }} To {{ Carbon\Carbon::parse($to)->format('d M Y') }}</div>
    <?php if (count($processedData) > 0): ?>
    <div style="margin-top: 10px;">
        <p>Total Groups: <?php echo count($processedData); ?> | Total Items: <?php echo array_sum(array_map('count', $processedData)); ?></p>
    </div>
    <?php endif; ?>

</div>

<table>
    <thead>
    <tr>
        <th width="5%">SI No</th>
        <th width="25%">Group Name</th>
        <th width="50%">Items Name</th>
        <th width="20%" class="text-right">Qty</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $siNo = 1;
    $grandTotal = 0;

    foreach ($processedData as $groupName => $fgItems):
        $groupFirst = true;
        $groupTotal = 0;

        // Calculate group total
        foreach ($fgItems as $itemData) {
            $groupTotal += $itemData['quantity'];
        }
        ?>
        <!-- Group Header Row -->
    <tr class="group-row">
        <td><?php echo $siNo; ?></td>
        <td><?php echo $groupName; ?></td>
        <td></td>
        <td></td>
    </tr>
        <?php $siNo++; ?>

        <!-- FG Items within Group -->
        <?php foreach ($fgItems as $fgName => $fgData): ?>
    <tr class="fg-row">
        <td></td>
        <td></td>
        <td><?php echo $fgName; ?></td>
        <td class="text-right"><?php echo number_format($fgData['quantity'], 2); ?></td>
    </tr>
        <?php
             $grandTotal += $fgData['quantity'];
          endforeach;
        ?>
    <tr>
        <td></td>
        <td></td>
        <td><strong>Group Total</strong></td>
        <td class="text-right"><strong><?php echo number_format($groupTotal, 2); ?></strong></td>
    </tr>

        <!-- Spacing between groups -->
        <?php if (next($processedData)): ?>
         <tr><td colspan="4" style="border: none; background-color: transparent;">&nbsp;</td></tr>

    <?php endif; ?>

    <?php endforeach; ?>

        <!-- Grand Total Row -->
    <tr class="grand-total">
        <td colspan="3" class="text-right"><strong>Grand Total</strong></td>
        <td class="text-right"><strong><?php echo number_format($grandTotal, 2); ?></strong></td>
    </tr>

    </tbody>
</table>


</body>
</html>
