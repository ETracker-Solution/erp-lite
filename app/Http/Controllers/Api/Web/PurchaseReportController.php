<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfInventory;
use App\Models\InventoryAdjustment;
use App\Models\InventoryTransaction;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class PurchaseReportController extends Controller
{

    public function index()
    {
        return view('purchase_report.index');
    }

    public function create()
    {

        $report_header = 'FG Wastage Report';
        $page_title = false;

        $startDate = Carbon::parse(\request()->from_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $endDate = Carbon::parse(\request()->to_date)->format('Y-m-d') ?? Carbon::now()->format('Y-m-d');
        $type = 'FG';

        $report_type = \request()->report_type;
        $store_id = \request()->store_id;


        $purchases = Purchase::with(['items.product', 'supplier'])
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('purchase_date', [$startDate, $endDate]);
            })
            ->get();

        $report = $purchases->map(function ($purchase) {
            return [
                'supplier_name' => $purchase->supplier->name,
                'purchase_id' => $purchase->id,
                'purchase_date' => $purchase->purchase_date,
                'items' => $purchase->items->map(function ($item) {
                    return [
                        'product_id' => $item->product->id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'total_value' => $item->quantity * $item->price,
                    ];
                }),
                'total_quantity' => $purchase->items->sum('quantity'),
                'total_value' => $purchase->items->sum(function ($item) {
                    return $item->quantity * $item->price;
                }),
            ];
        });

       return $report;
        $getPost = DB::select($statement);

        if (!count($getPost) > 0) {
            return false;
        }
        $columns = array_keys((array)$getPost[0]);
        $data = [
            'dateRange' => $toDate. ' -  ' . $fromDate,
            'data' => $getPost,
            'page_title' => $page_title,
            'columns' => $columns,
            'report_header' => $report_header
        ];
        $pdf = Pdf::loadView('common.report_main', $data);
        $pdf->stream();
    }
}
