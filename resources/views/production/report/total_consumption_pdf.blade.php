<?php
// Assuming $totalConsumption contains your data
$processedData = [];
$totalOverall = 0;

foreach ($totalConsumption as $transaction) {
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
            'rm_groups' => [] // Changed from rm_items to rm_groups for grouping
        ];
    }

    // Add quantity
    $processedData[$groupName][$fgName]['quantity'] += $quantity;

    // Process RM consumption and group by parent
    foreach ($fgItem->productionRecipes as $recipe) {
        $rmItem = $recipe->coi;

        // Skip if coi is empty
        if (!$rmItem) continue;

        $rmParentName = $rmItem->parent->name ?? 'Uncategorized RM';
        $rmName = $rmItem->name;
        $unit = $rmItem->unit->name ?? '';
        $stockCost = $rmItem->price ?? 0;
        $rmQuantity = $recipe->qty * $quantity;

        // Initialize RM parent group if not exists
        if (!isset($processedData[$groupName][$fgName]['rm_groups'][$rmParentName])) {
            $processedData[$groupName][$fgName]['rm_groups'][$rmParentName] = [
                'parent_name' => $rmParentName,
                'items' => []
            ];
        }

        // Initialize RM item if not exists
        if (!isset($processedData[$groupName][$fgName]['rm_groups'][$rmParentName]['items'][$rmName])) {
            $processedData[$groupName][$fgName]['rm_groups'][$rmParentName]['items'][$rmName] = [
                'unit' => $unit,
                'stock_cost' => $stockCost,
                'total_quantity' => 0
            ];
        }

        // Add to RM total quantity
        $processedData[$groupName][$fgName]['rm_groups'][$rmParentName]['items'][$rmName]['total_quantity'] += $rmQuantity;
    }
}

// Sort by group name
ksort($processedData);

?>

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
        .group-row { background-color: #e6f3ff; font-weight: bold; }
        .fg-row { background-color: #f9f9f9; }
        .rm-group-row {
            background-color: #fff3e6;
            font-weight: bold;
            border-top: 1px solid #ccc;
        }
        .rm-item-row {
            padding-left: 60px;
            font-size: 0.9em;
            background-color: #fff;
        }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #e6e6e6; }
        .indent-1 { padding-left: 30px; }
        .indent-2 { padding-left: 60px; }
    </style>
</head>
<body>
<div class="report-header">
    <div class="report-title">Total RM Consumption Report</div>
    <div class="report-date">{{ Carbon\Carbon::parse($from)->format('d M Y') }} To {{ Carbon\Carbon::parse($to)->format('d M Y') }}</div>
</div>
<?php
// Calculate summary by RM group
$rmGroupSummary = [];
foreach ($processedData as $fgGroupName => $fgItems) {
    foreach ($fgItems as $fgName => $fgData) {
        foreach ($fgData['rm_groups'] as $rmGroupName => $rmGroupData) {
            foreach ($rmGroupData['items'] as $rmName => $rmData) {
                if (!isset($rmGroupSummary[$rmGroupName])) {
                    $rmGroupSummary[$rmGroupName] = [
                        'total_items' => 0,
                        'groups' => []
                    ];
                }
                if (!isset($rmGroupSummary[$rmGroupName]['groups'][$rmName])) {
                    $rmGroupSummary[$rmGroupName]['groups'][$rmName] = [
                        'total_quantity' => 0,
                        'unit' => $rmData['unit']
                    ];
                }
                $rmGroupSummary[$rmGroupName]['groups'][$rmName]['total_quantity'] += $rmData['total_quantity'];
                $rmGroupSummary[$rmGroupName]['total_items']++;
            }
        }
    }
}
?>

@if(!empty($rmGroupSummary))
    <!-- Summary by RM Group -->
    <div style="margin-top: 10px;">
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
                <?php foreach ($rmGroupSummary as $groupName => $groupData): ?>
                <?php $firstItem = true; ?>
                <?php foreach ($groupData['groups'] as $itemName => $itemData): ?>
            <tr>
                <td><?php echo $firstItem ? $groupName : ''; ?></td>
                <td><?php echo $itemName; ?></td>
                <td><?php echo $itemData['unit']; ?></td>
                <td class="text-right"><?php echo number_format($itemData['total_quantity'], 4); ?></td>
            </tr>
                <?php $firstItem = false; ?>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">
                    Group Total Items: <?php echo $groupData['total_items']; ?>
                </td>
            </tr>
            <tr><td colspan="4" style="border: none; height: 5px;"></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
@endif

</body>
</html>

