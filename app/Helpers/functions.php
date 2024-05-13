<?php


use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        case 'picked':
            return '<div class="badge badge-primary">' . ucfirst($status) . '</div>';
        case 'assigned':
            return '<div class="badge badge-info">' . ucfirst($status) . '</div>';
        case 'accepted':
        case 'active':
        case 'delivered':
            return '<div class="badge badge-success">' . ucfirst($status) . '</div>';
        case 'return':
            return '<div class="badge badge-light-danger">' . ucfirst($status) . '</div>';
        case 'cancelled':
            return '<div class="badge badge-glow badge-danger">' . ucfirst($status) . '</div>';
        case 'inactive':
            return '<div class="badge badge-danger">' . ucfirst($status) . '</div>';
        case 'pending':
            return '<div class="badge badge-warning">Pending</div>';
        case 'hold':
            return '<div class="badge badge-light-warning">Hold</div>';
        case 'enable':
            return '<div class="badge badge-glow badge-success">Enable</div>';
        case 'disable':
            return '<div class="badge badge-glow badge-warning">Disable</div>';
        case 'transit':
            return '<div class="badge badge-glow badge-info">Transit</div>';
        case 'partial':
            return '<div class="badge badge-pill badge-glow badge-primary">Partial Collection</div>';
        case 'wait_for_pickup':
            return '<div class="badge badge-pill badge-glow badge-warning">Wait for Pickup</div>';
    }
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
    return $item ? (int) $item->uid + 1 : 1;
}
