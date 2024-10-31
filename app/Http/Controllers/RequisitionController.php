<?php

namespace App\Http\Controllers;

use App\Classes\RequisitionNumber;
use App\Http\Requests\StoreRequisitionRequest;
use App\Http\Requests\UpdateRequisitionRequest;
use App\Libraries\SaleUtil;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Store;
use App\Services\ExportService;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class RequisitionController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function exportRequisition($type)
    {
        $exportableData = $this->getRequisitionData();
        $viewFileName = 'todays_requisition';
        $filenameToDownload = date('ymdHis') . '_todays_requisition';
        return $this->exportService->exportFile($type, $viewFileName, $exportableData, $filenameToDownload, 'L'); // L stands for Landscape, if Portrait needed, just remove this params

    }

    public function index()
    {

        if (\request()->ajax()) {
            $data = $this->getFilteredData();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('requisition.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }
        return view('requisition.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $from_stores = Store::where(['type' => 'FG', 'doc_type' => 'outlet', 'status' => 'active', 'doc_id' => \auth()->user()->employee->outlet_id])->get();
        } else {
            $from_stores = Store::where(['type' => 'FG', 'doc_type' => 'outlet','status' => 'active'])->get();
        }

        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'from_stores' => $from_stores,
            'to_stores' => Store::where(['type' => 'FG', 'doc_type' => 'factory','status' => 'active'])->get(),
            'serial_no' => RequisitionNumber::serial_number()
        ];
        return view('requisition.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequisitionRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $requisition = Requisition::query()->create($data);
            $products = $request->get('products');
            foreach ($products as $row) {
                $requisition->items()->create($row);
            }
            DB::commit();
            Toastr::success('Requisition Entry Successful!.', '', ["progressBar" => true]);
            return redirect()->route('requisitions.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressbar" => true]);
            return back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $requisition = Requisition::findOrFail(decrypt($id));
        return view('requisition.show', compact('requisition'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'from_stores' => Store::where(['type' => 'FG', 'doc_type' => 'outlet','status' => 'active'])->get(),
            'to_stores' => Store::where(['type' => 'FG', 'doc_type' => 'factory','status' => 'active'])->get(),
            'requisition' => Requisition::find(decrypt($id))
        ];
        return view('requisition.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequisitionRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $requisition = Requisition::find($id);
            $requisition->update($validated);
            RequisitionItem::where('requisition_id', $requisition->id)->delete();
            $products = $request->get('products');
            foreach ($products as $row) {
                $requisition->items()->create($row);
            }


            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('FG Requisition Updated Successful!.', '', ["progressbar" => true]);
        return redirect()->route('requisitions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Requisition::findOrFail($id)->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Requisition Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('requisitions.index');
    }

    public function pdfDownload($id)
    {
        $data = [
            'requisition' => Requisition::findOrFail(decrypt($id)),
        ];

        $pdf = PDF::loadView(
            'requisition.pdf',
            $data,
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin-left' => 1,

                '', // mode - default ''
                '', // format - A4, for example, default ''
                0, // font size - default 0
                '', // default font family
                1, // margin_left
                1, // margin right
                1, // margin top
                1, // margin bottom
                1, // margin header
                1, // margin footer
                'L', // L - landscape, P - portrait

            ]
        );
        $name = \Carbon\Carbon::now()->format('d-m-Y');

        return $pdf->stream($name . '.pdf');
    }

    public function todayRequisition()
    {
        $data = $this->getRequisitionData();
        return view('requisition.today_requisition', $data);
    }

    public function getRequisitionData()
    {

        $all_requisitions = \App\Models\Requisition::todayFGAvailableRequisitions(auth('web')->user()->employee->factory_id);

        $requisition_ids = collect($all_requisitions)->pluck('id')->toArray();
        $outlet_ids = collect($all_requisitions)->pluck('outlet_id')->toArray();

        $product_ids = RequisitionItem::whereIn('requisition_id', $requisition_ids)->whereNotNull('coi_id')->pluck('coi_id')->toArray();

        $headers = [
            'Group',
            'Product',
        ];
        $outlets = Outlet::with(['requisitions.items'])->select('id', 'name')->whereIn('id', $outlet_ids)->get();
        foreach ($outlets as $outlet) {
            $headers[] = $outlet->name;
        }
        $headers[] = 'Total';
        $headers[] = 'Current Stock';
        $headers[] = 'Production';
        $values = [];
// Fetch all products
        $products = ChartOfInventory::where('type', 'item')
            ->with('parent')
            ->where('rootAccountType', 'FG')
            ->whereIn('id', $product_ids)
            ->orderBy('parent_id')
            ->orderBy('id')
            ->get();

// Fetch all requisitions for the outlets in one query
        $outletIds = $outlets->pluck('id'); // Get all outlet IDs
        $requisitions = Requisition::whereIn('outlet_id', $outletIds)
            ->where('type', 'FG')
            ->where('status', 'approved')
            ->whereIn('delivery_status', ['pending', 'partial'])
            ->with(['items', 'deliveries.items'])
            ->get()
            ->groupBy('outlet_id'); // Group by outlet_id for easier access later

// Prepare values array
        $values = [];

// Fetch stock balances for stores (once for all products)
        $stores = auth()->user()->employee->factory->stores()->where('type', 'FG')->get();


        // Get all store and product IDs
        $storeIds = $stores->pluck('id')->toArray();
        $productIds = $products->pluck('id')->toArray();

// Fetch store product balances in one query
        $storeStocks = fetchStoreProductBalances($productIds, $storeIds);


        foreach ($products as $key => $product) {
            $totalQty = 0;
            $current_stock = 0;
            $req_qty = 0;
            $delivered_qty = 0;
            $preOrderDeliveredQty = 0;
            $values[$key]['group_name'] = $product->parent->name;
            $values[$key]['product_name'] = $product->name;

            $delivered_qty += $product->requisitionDeliveryItems()->whereHas('requisitionDelivery', function ($q){
                return $q->where('status','completed');
            })->sum('quantity');

            $preOrderDeliveredQty += $product->preOrderItems()->whereHas('preOrder', function ($q){
                return $q->where('status','delivered');
            })->sum('quantity');

            foreach ($outlets as $outlet) {
                $outlet_req_qty = 0;
                $outlet_req_delivery_qty = 0;
                // Use the pre-fetched requisitions, grouped by outlet
                if (isset($requisitions[$outlet->id])) {
                    foreach ($requisitions[$outlet->id] as $req) {
                        $req_qty += $req->items->where('coi_id', $product->id)->sum('quantity');
                        $outlet_req_qty += $req->items->where('coi_id', $product->id)->sum('quantity');
                        foreach ($req->deliveries as $delivery) {
//                            $delivered_qty += $delivery->items->where('coi_id', $product->id)->sum('quantity');
                            $outlet_req_delivery_qty += $delivery->items->where('coi_id', $product->id)->sum('quantity');
                        }
                    }
                }

                $values[$key]['product_quantity'][] = $outlet_req_qty - $outlet_req_delivery_qty;
                $totalQty += ($outlet_req_qty - $outlet_req_delivery_qty);
            }

            // Calculate current stock for the specific product across all stores
            foreach ($stores as $store) {
                $current_stock += $storeStocks[$store->id][$product->id] ?? 0;
            }

            $diff = $req_qty - $current_stock;

            $values[$key]['total'] = $totalQty;
            $values[$key]['current_stock'][] = max(($current_stock - $delivered_qty - $preOrderDeliveredQty),0);
            $values[$key]['productionable'][] = max($diff, 0);

            if (($req_qty - $delivered_qty) == 0) {
                unset($values[$key]);
            }
        }

        return [
            'products' => $products,
            'outlets' => $outlets,
            'headers' => $headers,
            'values' => $values
        ];
    }

    public function updateStatus(Request $request, $id)
    {
        $req = Requisition::findOrFail($id);
        $req->update(['status' => $request->status]);
        Toastr::success('Requisition Approved Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('requisitions.index');
    }

    private function getFilteredData(){
        $data = Requisition::with('fromStore', 'toStore')->where('type', 'FG');
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $data = $data->where(['outlet_id' => \auth()->user()->employee->outlet_id]);
        }
        if (\request()->filled('status')){
            $data = $data->where('status',\request()->status);
        }
        if (\request()->filled('from_date') && \request()->filled('to_date')){
            $from_date = Carbon::parse(request()->from_date)->format('Y-m-d');
            $to_date = Carbon::parse(request()->to_date)->format('Y-m-d');
            $data = $data->whereDate('date','>=',$from_date)->whereDate('date','<=',$to_date);
        }
        return $data->latest();
    }
}
