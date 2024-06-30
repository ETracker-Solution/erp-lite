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
        case 'picked':
        case 'draft':
            return '<span class="badge badge-primary">' . ucfirst($status) . '</span>';
        case 'assigned':
            return '<span class="badge badge-info">' . ucfirst($status) . '</span>';
        case 'accepted':
        case 'active':
        case 'delivered':
        case 'completed':
        case 'received':
        case 'final':
            return '<span class="badge badge-success">' . ucfirst($status) . '</span>';
        case 'return':
            return '<span class="badge badge-light-danger">' . ucfirst($status) . '</span>';
        case 'cancelled':
            return '<span class="badge badge-glow badge-danger">' . ucfirst($status) . '</span>';
        case 'inactive':
            return '<span class="badge badge-danger">' . ucfirst($status) . '</span>';
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

function generateInvoiceCode($storeName)
{
    $outlet = Outlet::find($storeName);
    $storeName = str_replace(',',' ',trim($outlet->name));
    $words = $storeName;
    $acronym = "";

    $acronym .= mb_substr($words, 0, 3).mb_substr($words, -1);

    $nameWithDate = $acronym.date('ym');
    $lastCode = Sale::where('invoice_number','like','%'.$nameWithDate.'%')->orderBy('invoice_number','DESC')->first();
    if ($lastCode){
        $last3Digits = substr($lastCode->invoice_number,-3)+1;
    }else{
        $last3Digits = 001;
    }
    $code = $acronym.date('ym').str_pad($last3Digits, 3, 0, STR_PAD_LEFT);

    if (Sale::where('invoice_number',$code)->exists()){
        generateInvoiceCode($storeName);
    }
    return $code;
}