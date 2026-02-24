<?php

// Assuming $allConsumption contains your data
$processedData = [];
$totalOverall = 0;

foreach ($preOrderConsumption as $transaction) {
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

// Sort by group name
ksort($processedData);

?>

    <!-- HTML/PHP for PDF Report -->
<!DOCTYPE html>
<html>
<thead>
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
    .rm-row { padding-left: 40px; font-size: 0.9em; }
    .text-right { text-align: right; }
    .total-row { font-weight: bold; background-color: #e6e6e6; }
</style>
</thead>
<body>
<div class="report-header">
    <div class="report-title">Pre Order Wise Consumption Report</div>
    <div class="report-date">Store: {{ $store ?? '' }}</div>
    <div class="report-date">{{ Carbon\Carbon::parse($from)->format('d M Y') }} To {{ Carbon\Carbon::parse($to)->format('d M Y') }}</div>
</div>

<table>
    <thead>
    <tr>
        <th>SI No</th>
        <th>Group Name</th>
        <th>Items Name</th>
        <th>Qty</th>
        <th colspan="3">RM Consumption</th>
    </tr>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>Item</th>
        <th>Unit</th>
        <th>Cost</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $siNo = 1;
    $groupTotal = 0;

    foreach ($processedData as $groupName => $fgItems):
        $groupFirst = true;
        $groupItemCount = count($fgItems);
        $itemIndex = 0;

    foreach ($fgItems as $fgName => $fgData):
        $itemIndex++;
        $rmCount = count($fgData['rm_items']);
        $rmIndex = 0;
        ?>
        <!-- FG Item Row -->
    <tr>
        <td><?php echo $siNo++; ?></td>
        <td><?php echo $groupFirst ? $groupName : ''; ?></td>
        <td><?php echo $fgName; ?></td>
        <td class="text-right"><?php echo number_format($fgData['quantity'], 2); ?></td>
        <td colspan="4"></td>
    </tr>

    <!-- RM Consumption Rows -->
        <?php foreach ($fgData['rm_items'] as $rmName => $rmData):
        $rmIndex++;
        ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><?php echo $rmName; ?></td>
        <td><?php echo $rmData['unit']; ?></td>
        {{--        <td class="text-right"><?php echo number_format($rmData['stock_cost'], 2); ?></td>--}}
        <td class="text-right"><?php echo number_format($rmData['total_quantity'], 4); ?></td>
    </tr>
    <?php endforeach; ?>

        <!-- Empty row for spacing if not last item -->
        <?php if ($itemIndex < $groupItemCount): ?>
    <tr style="border-bottom: 1px solid #ccc;"><td colspan="8"></td></tr>
    <?php endif; ?>

        <?php
        $groupFirst = false;
    endforeach;
        ?>
        <!-- Group Total Row -->
    <tr class="total-row">
        <td colspan="8" style="text-align: right;">
                <?php
                $groupTotal = array_sum(array_column($fgItems, 'quantity'));
                echo "Group Total: " . number_format($groupTotal, 2) . " items";
                ?>
        </td>
    </tr>
    <!-- Spacing between groups -->
    <tr><td colspan="8" style="border: none;">&nbsp;</td></tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
