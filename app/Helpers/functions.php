<?php

use App\Models\Outlet;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

function getTimeByFormat($time, $format = 'g:i:s A')
{
    if (!$time) {
        return '-';
    }
    return Carbon::parse($time)->format($format);
}


//function successRedirect($message, $route){
//    Toastr::success($message);
//    return redirect()->route($route);
//}
//
//function failureRedirect($message, $route){
//    Toastr::error($message);
//    return redirect()->route($route);
//}
//
function showStatus($status)
{
    switch ($status) {
        case 'adjusted':
        case 'draft':
            return '<span class="badge badge-primary">' . ucfirst($status) . '</span>';
        case 'assigned':
        case 'approved':
            return '<span class="badge badge-info">' . ucfirst($status) . '</span>';
        case 'accepted':
        case 'active':
        case 'delivered':
        case 'completed':
        case 'received':
        case 'final':
            return '<span class="badge badge-success">' . ucfirst($status) . '</span>';
        case 'return':
        case 'inactive':
        case 'returned':
        case 'rejected':
            return '<span class="badge badge-danger">' . ucfirst($status) . '</span>';
        case 'cancelled':
            return '<span class="badge badge-glow badge-danger">' . ucfirst($status) . '</span>';
        case 'pending':
            return '<span class="badge badge-warning">Pending</span>';
        case 'hold':
            return '<span class="badge badge-light-warning">Hold</span>';
        case 'enable':
            return '<span class="badge badge-glow badge-success">Enable</span>';
        case 'disable':
            return '<span class="badge badge-glow badge-warning">Disable</span>';
        case 'transit':
            return '<span class="badge badge-glow badge-info">Transit</span>';
        case 'partial':
            return '<span class="badge badge-pill badge-glow badge-primary">Partial Collection</span>';
        case 'wait_for_pickup':
            return '<span class="badge badge-pill badge-glow badge-warning">Wait for Pickup</span>';
        case 'decrease':
            return '<span class="badge badge-pill badge-glow badge-danger">Wastage</span>';
        case 'increase':
            return '<span class="badge badge-pill badge-glow badge-success">Increase</span>';
        case 'ready_to_delivery':
            return '<span class="badge badge-pill badge-glow badge-dark">' . ucwords(str_replace('_', ' ', $status)) . '</span>';
    }
}

function getDatesArrayFromDateRange(string $date_range)
{
    if (str_contains($date_range, ' to ')) {
        return explode(' to ', $date_range);
    }
    return [$date_range, $date_range];
}

function searchColumnByDateRange($object, string $column_name = 'created_at', $date_range = null)
{
    $date_range = $date_range ?? request()->date_range;
    return $object->whereBetween(DB::raw('date(' . $column_name . ')'), getDatesArrayFromDateRange($date_range));
}

function totalPreOrder($product_id)
{

    $totalQty = DB::table('pre_order_items')
        ->where('product_id', $product_id)
        ->groupBy('product_id')
        ->sum('quantity');
    return $totalQty;
}

function getDue($merchant_id)
{
    $dueList = DB::table('collections')
        ->where('merchant_id', $merchant_id)
        ->orderBy('id', 'desc')
        ->sum('net_payable');
    return $dueList;
}


function getNextId($model)
{
    $item = $model::latest('uid')->first();
    return $item ? (int)$item->uid + 1 : 1;
}

function getAllPermissions()
{
    return \Spatie\Permission\Models\Permission::pluck('name');
}

function getSettingValue($key)
{
    $setting = \App\Models\SystemConfig::where('key', $key)->first();
    if ($setting) {
        return $setting->value;
    }
    return null;
}

function storeValue($key, $value)
{
    $exists = \App\Models\SystemConfig::where('key', $key)->first();
    if ($exists) {
        $exists->update([
            'value' => $value
        ]);
    } else {
        \App\Models\SystemConfig::create([
            'key' => $key,
            'value' => $value ?? '',
        ]);
    }
}

function getFileNameAfterImageUpload(UploadedFile $image)
{
    $filename = null;
    $filename = date('Ymdmhs') . uniqid() . '.' . $image->getClientOriginalExtension();
    $image->move(public_path('/upload'), $filename);
    return $filename;
}

function getRequisitionQtyByProduct($product_id, $outlet_id)
{
    $req = \App\Models\Requisition::with('items')->where('date', date('Y-m-d'))->where('to_factory_id', auth('web')->user()->employee->factory_id)
        ->where(['outlet_id' => $outlet_id])->first();
    if ($req) {
        return $req->items()->where('coi_id', $product_id)->sum('quantity');
    }
    return 0;
}

function getStoreDocId($store_id)
{
    return \App\Models\Store::where(['id' => $store_id])->first()->doc_id;
}

function generateInvoiceCode($store_id)
{
    $outlet = Outlet::find($store_id);
    $outlet_name = str_replace(',', ' ', trim($outlet->name));
    $words = strtoupper($outlet_name);
    $acronym = "";

    $acronym .= mb_substr($words, 0, 3) . mb_substr($words, -1);

    $nameWithDate = $acronym . date('ym');
    $lastCode = Sale::where('invoice_number', 'like', '%' . $nameWithDate . '%')->orderBy('invoice_number', 'DESC')->first();
    if ($lastCode) {
        $last3Digits = (int)(substr($lastCode->invoice_number, -3)) + 1;
    } else {
        $last3Digits = 001;
    }
    $code = $acronym . date('ym') . str_pad($last3Digits, 3, 0, STR_PAD_LEFT);

    if (Sale::where('invoice_number', $code)->exists()) {
        generateInvoiceCode($outlet_name);
    }
    return $code;
}

function generateUniqueUUID($outlet_or_factory_id, $model, $column_name, $is_factory = false, $is_headOffice = false)
{
    if (!$is_headOffice) {
        $outlet = $is_factory ? \App\Models\Factory::find($outlet_or_factory_id) : Outlet::find($outlet_or_factory_id);
        $outlet_name = str_replace(',', ' ', trim($outlet->name));
    } else {
        $outlet_name = 'HO';
    }

    $words = strtoupper($outlet_name);
    $acronym = "";

    $length = 4;

    $acronym .= mb_substr($words, 0, $length) . mb_substr($words, -1);

    if (!$is_factory && !$is_headOffice) {
        $acronym = $outlet->name;
        if (!str_ends_with($acronym, '-')) {
            $acronym = $acronym . '-';
        }
    }
    if ($is_factory) {
        $acronym = 'CT-F-';

    }

    $nameWithDate = $acronym . date('ym');
    $lastCode = $model::where($column_name, 'like', '%' . $nameWithDate . '%')->orderBy($column_name, 'DESC')->first();
    if ($lastCode) {
        $last3Digits = (int)(substr($lastCode->$column_name, -$length)) + 1;
    } else {
        $last3Digits = 001;
    }
    $code = $acronym . date('ym') . str_pad($last3Digits, $length, 0, STR_PAD_LEFT);

    if ($model::where($column_name, $code)->exists()) {
        generateInvoiceCode($outlet_name);
    }
    return $code;
}

function getRequisitionQty($requisition_id, $product_id)
{
    $requisition = \App\Models\Requisition::find($requisition_id);
    $found = $requisition->items()->where('coi_id', $product_id)->first();
    if ($found) {
        return $found->quantity;
    } else {
        return 0;
    }
}

function getReturnedQty($sale_return_id, $coi_id)
{
    $sale_return = \App\Models\SalesReturn::find($sale_return_id);
    $found = $sale_return->items()->where('coi_id', $coi_id)->first();
    if ($found) {
        return $found->quantity;
    } else {
        return 0;
    }
}


function get_all_groups_report($date, $ac_type)
{
    return "SELECT
    CIP.name as `Group Name`,
    FORMAT(SUM(IT.quantity * IT.type),0) as `Balance Qty`,
    -- FORMAT((IT.amount / SUM(IT.quantity * IT.type)),0) as RATE,
    FORMAT(IT.amount,0) as `Value`
    FROM
    inventory_transactions IT
    JOIN
    chart_of_inventories CI ON CI.id = IT.coi_id
    JOIN
    chart_of_inventories CIP ON CIP.id = CI.parent_id
    WHERE
            IT.date <= '$date'
            AND CI.rootAccountType = '$ac_type'
    GROUP BY
    CIP.id";
}

function get_all_items_by_group($group_id, $date, $ac_type)
{
    return "SELECT
    `Item ID`,
    `Item Name`,
    `Balance Qty`,
    `Rate`,
    `Value`
FROM (
    SELECT
        IF(Group_Name = @prev_group, '', IFNULL(Group_Name, '')) AS `Group Name`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_ID, '')) AS `Item ID`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_Name, '')) AS `Item Name`,
        `Balance Qty`,
        IF(Subgroup_ID IS NULL, '', IFNULL(
        CASE
    WHEN '$ac_type' = 'RM' THEN IF(`AllQ` = 0, NULL,format( `AllA` / `AllQ`,0))
    ELSE FORMAT(ORate,0)
    END
        , ''))  AS `Rate`,
         CASE WHEN '$ac_type' = 'RM' THEN format(`Value`,0) ELSE format(( `Balance Qty` * ORate),0) END AS `Value`,
        @prev_group := Group_Name
    FROM (
        SELECT
            IF(CIP.name IS NULL, 'Total', CIP.name) AS Group_Name,
            CI.id AS Subgroup_ID,
            CI.name AS Subgroup_Name,
            SUM(IT.quantity * IT.type) AS `Balance Qty`,
            SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END) as `AllQ`,
            SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END) as `AllA`,
            CI.price as ORate,
            SUM(IT.amount) AS `Value`
        FROM
            inventory_transactions IT
        JOIN
            chart_of_inventories CI ON CI.id = IT.coi_id
        JOIN
            chart_of_inventories CIP ON CIP.id = CI.parent_id
         WHERE
            CIP.id = '$group_id'
            AND IT.date <= '$date'
            AND CI.rootAccountType = '$ac_type'
        GROUP BY
            CI.id WITH ROLLUP
    ) AS subquery,
    (SELECT @prev_group := null) AS prev
) AS result";
}

function get_all_items($date, $ac_type)
{
    return "SELECT
    `Group Name`,
    `Item ID`,
    `Item Name`,
    `Balance Qty`,
    `Rate`,
    `Value`
FROM (
    SELECT
        IF(Group_Name = @prev_group, '', IFNULL(Group_Name, '')) AS `Group Name`,
        IF(Subgroup_ID IS NULL, if(@is_last_row = 1, 'Grand Total','Total'), IFNULL(Subgroup_ID, '')) AS `Item ID`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_Name, '')) AS `Item Name`,
        `Balance Qty`,
        IF(Subgroup_ID IS NULL, '', IFNULL(
        CASE
    WHEN '$ac_type' = 'RM' THEN IF(`AllQ` = 0, NULL,format( `AllA` / `AllQ`,0))
    ELSE FORMAT(ORate,0)
    END
        , ''))  AS `Rate`,
        CASE WHEN '$ac_type' = 'RM' THEN format(`Value`,0) ELSE format(( `Balance Qty` * ORate),0) END AS `Value`,
        @prev_group := Group_Name,
        @is_last_row := CASE
            WHEN Subgroup_ID IS NULL THEN 1
            ELSE 0
        END
    FROM (
        SELECT
            IF(CIP.name IS NULL, 'Total', CIP.name) AS Group_Name,
            CI.id AS Subgroup_ID,
            CI.name AS Subgroup_Name,
            SUM(IT.quantity * IT.type) AS `Balance Qty`,
             SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END) as `AllQ`,
            SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END) as `AllA`,
            CI.price as ORate,
            SUM(IT.amount) AS `Value`
        FROM
            inventory_transactions IT
        JOIN
            chart_of_inventories CI ON CI.id = IT.coi_id
        JOIN
            chart_of_inventories CIP ON CIP.id = CI.parent_id
         WHERE
            IT.date <= '$date'
            AND CI.rootAccountType = '$ac_type'
        GROUP BY
            CIP.id, CI.id WITH ROLLUP
    ) AS subquery,
    (SELECT @prev_group := null, @is_last_row := 0) AS prev
) AS result";
}

function get_all_stores($date, $ac_type)
{
//    if ($ac_type == 'FG'){
//        return all_store_fg_report_query($date);
//    }
    return "SELECT
    `Store Name`,
    -- `Group Name`,
    -- `Item ID`,
    -- `Item Name`,
    `Balance Qty`,
    `Value`
FROM (
    SELECT
		IF(Store_Name = @prev_store, '', IFNULL(Store_Name, '')) AS `Store Name`,
        IF(Group_Name = @prev_group, '', IFNULL(Group_Name, '')) AS `Group Name`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_ID, '')) AS `Item ID`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_Name, '')) AS `Item Name`,
        `Balance Qty`,
        IF(Subgroup_ID IS NULL, '', IFNULL(
        CASE
    WHEN '$ac_type' = 'RM' THEN IF(`AllQ` = 0, NULL,format( `AllA` / `AllQ`,0))
    ELSE FORMAT(ORate,0)
    END
        , ''))  AS `Rate`,
      IF(Subgroup_ID IS NULL,format(@total_a,0),CASE WHEN '$ac_type' = 'RM' THEN format(`Value`,0) ELSE format(( `Balance Qty` * ORate),0) END) as`Value`,
        @prev_group := Group_Name,
        @prev_store:= Store_Name,
        @total_a := @total_a + `Value`
    FROM (
        SELECT
			ST.id AS Store_ID,
            ST.name AS Store_Name,
            IF(CIP.name IS NULL, 'Total', CIP.name) AS Group_Name,
            CI.id AS Subgroup_ID,
            CI.name AS Subgroup_Name,
            SUM(IT.quantity * IT.type) AS `Balance Qty`,
            SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END) as `AllQ`,
            SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END) as `AllA`,
            CI.price as ORate,
            SUM(IT.quantity * IT.type) * (SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END)/SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END)) AS `Value`
        FROM
            inventory_transactions IT
        JOIN
            chart_of_inventories CI ON CI.id = IT.coi_id
        JOIN
            chart_of_inventories CIP ON CIP.id = CI.parent_id
        LEFT JOIN
			stores ST on ST.id  = IT.store_id
		WHERE
            IT.date <= '$date'
            AND CI.rootAccountType = '$ac_type'
        GROUP BY
            IT.store_id WITH ROLLUP
    ) AS subquery,
    (SELECT @prev_group := null, @prev_store:= null, @total_a :=0) AS prev
) AS result";
}

function all_store_fg_report_query($date){
    return "
   select
	s.name as 'STORE NAME',
	SUM(it.quantity * it.`type`) as 'Balance QTY',
	FORMAT(SUM(it.quantity * it.type) * (SUM(CASE WHEN it.type = 1 THEN it.amount ELSE 0 END)/ SUM(CASE WHEN it.type = 1 THEN it.quantity ELSE 0 END)), 0) AS `Value`
from
	inventory_transactions it
join chart_of_inventories coi
on
	coi.id = it.coi_id
left join stores s
on
	s.id = it.store_id
WHERE
	it.date <= '$date'
	AND coi.rootAccountType = 'FG'
group by
	s.id
UNION ALL
select
	'TOTAL' as 'STORE NAME',
	SUM(it.quantity * it.`type`) as 'Balance QTY',
	FORMAT(SUM(it.quantity * it.type) * (SUM(CASE WHEN it.type = 1 THEN it.amount ELSE 0 END)/ SUM(CASE WHEN it.type = 1 THEN it.quantity ELSE 0 END)), 0) AS `Value`
from
	inventory_transactions it
join chart_of_inventories coi
on
	coi.id = it.coi_id
WHERE
	it.date <= '$date'
	AND coi.rootAccountType = 'FG'
    ";
}

function get_all_items_by_store($store_id, $date, $ac_type)
{
    if ($ac_type == 'FG') {
        return get_all_items_by_fg_store($store_id, $date);
    }
    return "SELECT
    `Group Name`,
    `Item ID`,
    `Item Name`,
    `Balance Qty`,
    `Rate`,
    `Value`
FROM (
    SELECT
		IF(Store_Name = @prev_store, '', IFNULL(Store_Name, '')) AS `Store Name`,
        IF(Group_Name = @prev_group, '', IFNULL(Group_Name, '')) AS `Group Name`,
        IF(Subgroup_ID IS NULL, if(@is_last_row = 1, 'Grand Total','Total'), IFNULL(Subgroup_ID, '')) AS `Item ID`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_Name, '')) AS `Item Name`,
        `Balance Qty`,
        IF(Subgroup_ID IS NULL, '', IFNULL(
        CASE
    WHEN '$ac_type' = 'RM' THEN IF(`AllQ` = 0, NULL,format( `AllA` / `AllQ`,0))
    ELSE FORMAT(ORate,0)
    END
        , ''))  AS `Rate`,
      IF(Subgroup_ID IS NULL, '', IFNULL(
        CASE
    WHEN '$ac_type' = 'RM' THEN FORMAT( `Value`,0)
    ELSE FORMAT((ORate * `Balance Qty`),0)
    END
        , ''))  AS `Value`,
        @prev_group := Group_Name,
         @prev_store:= Store_Name,
        @total_a := @total_a + `Value`,
            @is_last_row := CASE
            WHEN Subgroup_ID IS NULL THEN 1
            ELSE 0
        END
    FROM (
        SELECT
			ST.id AS Store_ID,
            ST.name AS Store_Name,
            IF(CIP.name IS NULL, 'Total', CIP.name) AS Group_Name,
            CI.id AS Subgroup_ID,
            CI.name AS Subgroup_Name,
            SUM(IT.quantity * IT.type) AS `Balance Qty`,
            SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END) as `AllQ`,
            SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END) as `AllA`,
            CI.price as ORate,
            SUM(IT.quantity * IT.type) * (SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END)/SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END)) AS `Value`
        FROM
            inventory_transactions IT
        JOIN
            chart_of_inventories CI ON CI.id = IT.coi_id
        JOIN
            chart_of_inventories CIP ON CIP.id = CI.parent_id
        LEFT JOIN
			stores ST on ST.id  = IT.store_id
        WHERE
			IT.store_id = '$store_id'
			AND IT.date <= '$date'
			 AND CI.rootAccountType = '$ac_type'
        GROUP BY
             CIP.id, CI.id WITH ROLLUP
    ) AS subquery,
    (SELECT @prev_group := null, @prev_store:= null, @total_a :=0, @is_last_row := 0) AS prev
) AS result";
}


function get_all_items_by_fg_store($store_id, $date)
{
    return "WITH TransitStockRequisition AS (
    SELECT
        rdi.coi_id,
        SUM(rdi.quantity) AS transit_stock
    FROM
        requisition_delivery_items rdi
    JOIN
        requisition_deliveries rd ON rd.id = rdi.requisition_delivery_id
    WHERE
        rd.status = 'completed'
        AND rd.type = 'FG'
        AND rd.from_store_id = '$store_id'
    GROUP BY
        rdi.coi_id
),
TransitStockInventory AS (
    SELECT
        iti.coi_id,
        SUM(iti.quantity) AS transit_stock
    FROM
        inventory_transfer_items iti
    JOIN
        inventory_transfers it ON it.id = iti.inventory_transfer_id
    WHERE
        it.status = 'completed'
        AND it.type = 'FG'
         AND it.from_store_id = '$store_id'
    GROUP BY
        iti.coi_id
),
TransitStockPreOrder AS (
    SELECT
        poi.coi_id,
        SUM(poi.quantity) AS transit_stock
    FROM
        pre_order_items poi
    JOIN
        pre_orders po ON po.id = poi.pre_order_id
    WHERE
        po.status = 'delivered'
         AND po.factory_delivery_store_id = '$store_id'
    GROUP BY
        poi.coi_id
),
CombinedTransitStock AS (
    SELECT
        coi_id,
        SUM(transit_stock) AS total_transit_stock
    FROM (
        SELECT * FROM TransitStockRequisition
        UNION ALL
        SELECT * FROM TransitStockInventory
        UNION ALL
        SELECT * FROM TransitStockPreOrder
    ) AS combined
    GROUP BY
        coi_id
)
SELECT
    `Group Name`,
    `Item ID`,
    `Item Name`,
    `Balance Qty`,
    IFNULL(ts.total_transit_stock, 0) AS `Transit Stock`,
    `Rate`,
    `Value`
FROM (
    SELECT
        IF(Store_Name = @prev_store, '', IFNULL(Store_Name, '')) AS `Store Name`,
        IF(Group_Name = @prev_group, '', IFNULL(Group_Name, '')) AS `Group Name`,
        IF(Subgroup_ID IS NULL, IF(@is_last_row = 1, 'Grand Total', 'Total'), IFNULL(Subgroup_ID, '')) AS `Item ID`,
        IF(Subgroup_ID IS NULL, '', IFNULL(Subgroup_Name, '')) AS `Item Name`,
        `Balance Qty`,
        IF(Subgroup_ID IS NULL, '', IFNULL(
            CASE
                WHEN 'FG' = 'RM' THEN IF(`AllQ` = 0, NULL, FORMAT(`AllA` / `AllQ`, 0))
                ELSE FORMAT(ORate, 0)
            END,
        '')) AS `Rate`,
        IF(Subgroup_ID IS NULL, '', IFNULL(
            CASE
                WHEN 'FG' = 'RM' THEN FORMAT(`Value`, 0)
                ELSE FORMAT((ORate * `Balance Qty`), 0)
            END,
        '')) AS `Value`,
        @prev_group := Group_Name,
        @prev_store := Store_Name,
        @total_a := @total_a + `Value`,
        @is_last_row := CASE
            WHEN Subgroup_ID IS NULL THEN 1
            ELSE 0
        END
    FROM (
        SELECT
            ST.id AS Store_ID,
            ST.name AS Store_Name,
            IF(CIP.name IS NULL, 'Total', CIP.name) AS Group_Name,
            CI.id AS Subgroup_ID,
            CI.name AS Subgroup_Name,
            SUM(IT.quantity * IT.type) AS `Balance Qty`,
            SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END) AS `AllQ`,
            SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END) AS `AllA`,
            CI.price AS ORate,
            SUM(IT.quantity * IT.type) * (
                SUM(CASE WHEN IT.type = 1 THEN IT.amount ELSE 0 END) /
                SUM(CASE WHEN IT.type = 1 THEN IT.quantity ELSE 0 END)
            ) AS `Value`
        FROM
            inventory_transactions IT
        JOIN
            chart_of_inventories CI ON CI.id = IT.coi_id
        JOIN
            chart_of_inventories CIP ON CIP.id = CI.parent_id
        LEFT JOIN
            stores ST ON ST.id = IT.store_id
        WHERE
            IT.store_id = '$store_id'
            AND IT.date <= '$date'
            AND CI.rootAccountType = 'FG'
        GROUP BY
            CIP.id, CI.id WITH ROLLUP
    ) AS subquery
) AS result
LEFT JOIN
    CombinedTransitStock ts ON ts.coi_id = result.`Item ID`;
";
}

function testFGreport($store_id, $date)
{
    $data = [];
    $parents = \App\Models\ChartOfInventory::with([
        'parent',
        'inventoryTransactions',
        'requisitionDeliveryItems.requisitionDelivery',
        'inventoryTransferItems.inventoryTransfer',
        'preOrderItems.preOrder'])
        ->whereHas('parent')
        ->where(['rootAccountType' => 'FG', 'type' => 'item'])
        ->orderBy('parent_id')
        ->get()
        ->groupBy('parent_id');
    $grand_total_transit_stock = 0;
    $grand_total_balance_qty = 0;
    foreach ($parents as $parent_id => $parent) {
        $parent_total_transit_stock = 0;
        $parent_total_balance_qty = 0;
        foreach ($parent as $key => $item) {
            $transit_delivery_qty = $item->requisitionDeliveryItems()->whereHas('requisitionDelivery', function ($q) use ($store_id) {
                return $q->where(['status' => 'completed', 'type' => 'FG', 'from_store_id' => $store_id]);
            })->sum('quantity');

            $transit_transfer_qty = $item->inventoryTransferItems()->whereHas('inventoryTransfer', function ($q) use ($store_id) {
                return $q->where(['status' => 'pending', 'type' => 'FG', 'from_store_id' => $store_id]);
            })->sum('quantity');

            $transit_pre_order_qty = $item->preOrderItems()->whereHas('preOrder', function ($q) use ($store_id) {
                return $q->where(['status' => 'delivered', 'factory_delivery_store_id' => $store_id]);
            })->sum('quantity');

            $total_transit_stock = $transit_delivery_qty + $transit_transfer_qty + $transit_pre_order_qty;
            $main_balance = $item->inventoryTransactions()->where('store_id', $store_id)->whereDate('date', '<=', $date)->sum(DB::raw('type * quantity'));

            $parent_total_transit_stock += $total_transit_stock;
            $parent_total_balance_qty += $main_balance;

            $data[] = [
                'Group Name' => $item->parent->name,
                'Item ID' => $item->id,
                'Item Name' => $item->name,
                'Transit Stock' => $total_transit_stock,
                'Balance Qty' => number_format($main_balance, 2),
                'Rate' => number_format($item->price, 2, '.', ','),
                'Value' => number_format($item->price * $main_balance, 2, '.', ','),
            ];

        }

        $grand_total_transit_stock += $parent_total_transit_stock;
        $grand_total_balance_qty += $parent_total_balance_qty;
        $data[] = [
            'Group Name' => '', // This could be set to the parent's name or left blank
            'Item ID' => '',
            'Item Name' => $parent[0]->parent->name .' Total',
            'Transit Stock' => $parent_total_transit_stock,
            'Balance Qty' => number_format($parent_total_balance_qty, 2),
            'Rate' => '', // Total rate is typically not used
            'Value' => ''
        ];
//        return $data;
    }
    $data[] = [
        'Group Name' => '', // This could be set to the parent's name or left blank
        'Item ID' => '',
        'Item Name' => 'Grand Total',
        'Transit Stock' => $grand_total_transit_stock,
        'Balance Qty' => number_format($grand_total_balance_qty, 2),
        'Rate' => '', // Total rate is typically not used
        'Value' => '0'
    ];
    return $data;
}

// function extracode()
// {
//     CASE
//     WHEN '$ac_type' = 'RM' THEN FORMAT((IT.amount / SUM(IT.quantity * IT.type)), 0)
//     ELSE FORMAT(rate,0)
//     END AS RATE,
// }

function showUserInfo($user){
    return $user->name . ' (' . $user->email . ') ';
}

function get_store_item_details($store_id, $item_id, $date)
{
    return "
    select ITT.date as DATE,
     (ITT.type * ITT.quantity) as QUANTITY,
     ITT.store_id as STORE_ID,
     ITT.coi_id as ITEM_ID,
CASE
	WHEN ITT.doc_type = 'FGP' THEN 'PRODUCTION'
    WHEN ITT.doc_type = 'FGOB' THEN 'OPENING BALANCE'
    WHEN ITT.doc_type = 'FGRD' THEN 'REQUISITION DELIVERY'
    WHEN ITT.doc_type = 'POS' THEN 'SALES'
    WHEN ITT.doc_type = 'FGIA' THEN 'INCREMENT/WASTAGE'
    ELSE ITT.doc_type
    END as ACTION
from inventory_transactions  ITT
where ITT.coi_id = $item_id AND ITT.store_id = $store_id
UNION ALL
SELECT
    'TOTAL' AS DATE,
    SUM(ITT.type * ITT.quantity) AS QUANTITY,
    $store_id as STORE_ID,
    $item_id as ITEM_ID,
    'SUM OF ALL TRANSACTION' AS ACTION
FROM inventory_transactions ITT
WHERE ITT.coi_id = $item_id AND ITT.store_id = $store_id group by ITT.coi_id
    ";
}
