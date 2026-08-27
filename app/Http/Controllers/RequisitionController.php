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
use App\Models\RequisitionDeliveryItem;
use App\Models\Store;
use App\Services\ExportService;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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

    public function exportFGRequisition($type)
    {
        $data = RequisitionItem::with('coi.parent', 'requisition.outlet')
        ->whereHas('requisition', function ($query) {
            $query->where('type', 'FG');
        })->latest();

        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $outlet_id = \auth()->user()->employee->outlet_id;
            $data = $data->whereHas('requisition', function ($query) use ($outlet_id) {
                $query->where('outlet_id', $outlet_id);
            });
        }

        if (\request()->filled(key: 'status')) {
            $status = \request()->status;

            $data = $data->whereHas('requisition', function ($query) use ($status) {
                $query->where('status', $status);
            });
        }

        if (\request()->filled('from_date') && \request()->filled('to_date')) {
            $from_date = Carbon::parse(request()->from_date)->format('Y-m-d');
            $to_date = Carbon::parse(request()->to_date)->format('Y-m-d');

            $data = $data->whereHas('requisition', function ($query) use ($from_date, $to_date) {
                $query->whereDate('date', '>=', $from_date)
                    ->whereDate('date', '<=', $to_date);
            });
        }


        $exportableData = [
            'requisitions' => $data->get()
        ];
        $viewFileName = 'fg_requisition';
        $filenameToDownload = date('ymdHis') . '_fg_requisition';
        return $this->exportService->exportFile($type, $viewFileName, $exportableData, $filenameToDownload, 'L'); // L stands for Landscape, if Portrait needed, just remove this params

    }

    public function index()
    {
        if (request()->ajax()) {
            $query = $this->buildIndexQuery();

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->addColumn('action', function ($row) {
                    return view('requisition.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
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
        $outletId = auth()->user()?->employee?->outlet_id;

        return view('requisition.create', [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'FG'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'from_stores' => Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'outlet', 'status' => 'active'])
                ->when($outletId, fn ($q) => $q->where('doc_id', $outletId))
                ->orderBy('name')
                ->get(['id', 'name']),
            'to_stores' => Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'factory', 'status' => 'active'])
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
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
        $requisition = $this->loadRequisitionForDisplay($id);
        $deliveredQtyByCoi = $this->deliveredQuantitiesByCoi($requisition);

        return view('requisition.show', compact('requisition', 'deliveredQtyByCoi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'from_stores' => Store::where(['type' => 'FG', 'doc_type' => 'outlet', 'status' => 'active'])->get(),
            'to_stores' => Store::where(['type' => 'FG', 'doc_type' => 'factory', 'status' => 'active'])->get(),
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
        $requisition = $this->loadRequisitionForDisplay($id);

        $pdf = PDF::loadView(
            'requisition.pdf',
            ['requisition' => $requisition],
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
        $all_requisitions = collect(\App\Models\Requisition::todayFGAvailableRequisitions(auth('web')->user()->employee->factory_id));
        $requisition_ids = $all_requisitions->pluck('id')->filter()->unique()->values();
        $outlet_ids = $all_requisitions->pluck('outlet_id')->filter()->unique()->values();

        $headers = [
            'Group',
            'Product',
        ];
        $outlets = Outlet::query()->select('id', 'name')->whereIn('id', $outlet_ids)->get();
        foreach ($outlets as $outlet) {
            $headers[] = $outlet->name;
        }
        $headers[] = 'Total';
        $headers[] = 'Current Stock';
        $headers[] = 'Production';

        if ($requisition_ids->isEmpty()) {
            return [
                'products' => collect(),
                'outlets' => $outlets,
                'headers' => $headers,
                'values' => [],
            ];
        }

        $requested = DB::table('requisition_items as ri')
            ->join('requisitions as r', 'r.id', '=', 'ri.requisition_id')
            ->whereIn('ri.requisition_id', $requisition_ids)
            ->whereNotNull('ri.coi_id')
            ->groupBy('r.outlet_id', 'ri.coi_id')
            ->select('r.outlet_id', 'ri.coi_id', DB::raw('SUM(ri.quantity) as qty'))
            ->get();

        $delivered = DB::table('requisition_delivery_items as rdi')
            ->join('requisition_deliveries as rd', 'rd.id', '=', 'rdi.requisition_delivery_id')
            ->join('requisitions as r', 'r.id', '=', 'rd.requisition_id')
            ->whereIn('rd.requisition_id', $requisition_ids)
            ->whereNotNull('rdi.coi_id')
            ->groupBy('r.outlet_id', 'rdi.coi_id')
            ->select('r.outlet_id', 'rdi.coi_id', DB::raw('SUM(rdi.quantity) as qty'))
            ->get();

        $remainingByOutletProduct = [];
        foreach ($requested as $row) {
            $remainingByOutletProduct[$row->outlet_id][$row->coi_id] = (float) $row->qty;
        }
        foreach ($delivered as $row) {
            $remainingByOutletProduct[$row->outlet_id][$row->coi_id] =
                ($remainingByOutletProduct[$row->outlet_id][$row->coi_id] ?? 0) - (float) $row->qty;
        }

        $product_ids = $requested->pluck('coi_id')->unique()->values();
        $products = ChartOfInventory::query()
            ->select('id', 'parent_id', 'name')
            ->with('parent:id,name')
            ->where('type', 'item')
            ->where('rootAccountType', 'FG')
            ->whereIn('id', $product_ids)
            ->orderBy('parent_id')
            ->orderBy('id')
            ->get();

        $storeIds = auth()->user()->employee->factory->stores()->where('type', 'FG')->pluck('id')->toArray();
        $stocks = transactionAbleStock($products, $storeIds, true);
        $stockByProduct = [];
        foreach ($stocks as $row) {
            $product = $row['product'];
            $productId = is_array($product) ? ($product['id'] ?? null) : ($product->id ?? null);
            $stockByProduct[$productId] = $row['stock'];
        }

        $values = [];
        foreach ($products as $key => $product) {
            $reqLeft = 0;
            $values[$key]['group_name'] = $product->parent->name ?? '';
            $values[$key]['product_name'] = $product->name;

            foreach ($outlets as $outlet) {
                $qty = $remainingByOutletProduct[$outlet->id][$product->id] ?? 0;
                $values[$key]['product_quantity'][] = $qty;
                $reqLeft += $qty;
            }

            if ($reqLeft == 0) {
                unset($values[$key]);
                continue;
            }

            $current_stock = $stockByProduct[$product->id] ?? 0;
            $values[$key]['total'] = $reqLeft;
            $values[$key]['current_stock'][] = $current_stock;
            $values[$key]['productionable'][] = max($reqLeft - $current_stock, 0);
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
        $req->update(['status' => $request->status,'approved_by' => $request->status == "approved" ? auth()->id() : null,]);
        Toastr::success('Requisition Approved Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('requisitions.index');
    }

    private function buildIndexQuery()
    {
        $query = Requisition::query()
            ->select([
                'requisitions.id',
                'requisitions.uid',
                'requisitions.date',
                'requisitions.status',
                'requisitions.delivery_status',
                'requisitions.from_store_id',
                'requisitions.to_store_id',
                'requisitions.created_at',
            ])
            ->with([
                'fromStore:id,name',
                'toStore:id,name',
            ])
            ->where('requisitions.type', 'FG');

        if (auth()->user()?->employee?->outlet_id) {
            $query->where('outlet_id', auth()->user()->employee->outlet_id);
        }
        if (request()->filled('status')) {
            $query->where('status', request()->status);
        }
        if (request()->filled('from_date') && request()->filled('to_date')) {
            $fromDate = Carbon::parse(request()->from_date)->format('Y-m-d');
            $toDate = Carbon::parse(request()->to_date)->format('Y-m-d');
            $query->whereDate('date', '>=', $fromDate)->whereDate('date', '<=', $toDate);
        }

        return $query->latest('requisitions.id');
    }

    private function getFilteredData()
    {
        $data = Requisition::with('fromStore', 'toStore')->where('requisitions.type', 'FG');
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $data = $data->where(['outlet_id' => \auth()->user()->employee->outlet_id]);
        }
        if (\request()->filled(key: 'status')) {
            $data = $data->where('status', \request()->status);
        }
        if (\request()->filled('from_date') && \request()->filled('to_date')) {
            $from_date = Carbon::parse(request()->from_date)->format('Y-m-d');
            $to_date = Carbon::parse(request()->to_date)->format('Y-m-d');
            $data = $data->whereDate('date', '>=', $from_date)->whereDate('date', '<=', $to_date);
        }
        return $data->latest();
    }

    public function getRequisitionData2()
    {

        $all_requisitions = Requisition::with(['deliveries.items', 'items'])
            ->where('to_factory_id', \auth()->user()->employee->factory_id)
            ->where('type', 'FG')
            ->where('status', 'approved')
            ->whereIn('delivery_status', ['pending', 'partial'])
            ->get();
        $new_product_ids = $all_requisitions->flatMap(function ($requisition) {
            return $requisition->items->whereNotNull('coi_id')->pluck('coi_id');
        })->unique()->values()->toArray();

        $new_outlet_ids = $all_requisitions->pluck('outlet_id')->toArray();

        $headers = [
            'Group',
            'Product',
        ];

        $outlets = Outlet::with(['requisitions:id,outlet_id', 'requisitions.items:id,quantity,coi_id,requisition_id'])->select('id', 'name')->whereIn('id', $new_outlet_ids)->get();

        foreach ($outlets as $outlet) {
            $headers[] = $outlet->name;
        }

        $headers[] = 'Total';
        $headers[] = 'Current Stock';
        $headers[] = 'Production';
        // Fetch all products
        $products = ChartOfInventory::where('type', 'item')
            ->with('parent:id,name')
            ->where('rootAccountType', 'FG')
            ->whereIn('id', $new_product_ids)
            ->select('id', 'parent_id', 'name')
            ->orderBy('parent_id')
            ->orderBy('id')
            ->get();

        $requisitions = $all_requisitions->groupBy('outlet_id');

        $values = [];

        $stores = auth()->user()->employee->factory->stores()->where('type', 'FG')->get();

        $storeIds = $stores->pluck('id')->toArray();

        $storeStocks = fetchStoreProductBalances($new_product_ids, $storeIds);

        foreach ($products as $key => $product) {
            $totalQty = 0;
            $current_stock = 0;
            $req_qty = 0;
            $delivered_qty = 0;
            $preOrderDeliveredQty = 0;

            $values[$key]['group_name'] = $product->parent->name;
            $values[$key]['product_name'] = $product->name;

            $delivered_qty += $product->requisitionDeliveryItems()->whereHas('requisitionDelivery', function ($q) {
                return $q->where('status', 'completed');
            })->sum('quantity');

            $preOrderDeliveredQty += $product->preOrderItems()->whereHas('preOrder', function ($q) {
                return $q->where('status', 'delivered');
            })->sum('quantity');

            foreach ($outlets as $outlet) {
                $outlet_req_qty = 0;
                $outlet_req_delivery_qty = 0;
                if (isset($requisitions[$outlet->id])) {
                    foreach ($requisitions[$outlet->id] as $req) {
                        $req_qty += $req->items->where('coi_id', $product->id)->sum('quantity');
                        $outlet_req_qty += $req->items->where('coi_id', $product->id)->sum('quantity');
                        foreach ($req->deliveries as $delivery) {
                            $outlet_req_delivery_qty += $delivery->items->where('coi_id', $product->id)->sum('quantity');
                        }
                    }
                }

                $values[$key]['product_quantity'][] = $outlet_req_qty - $outlet_req_delivery_qty;
                $totalQty += max(($outlet_req_qty - $outlet_req_delivery_qty), 0);
            }

            foreach ($stores as $store) {
                $current_stock += $storeStocks[$store->id][$product->id] ?? 0;
            }

            $current_stock = max(($current_stock - $delivered_qty - $preOrderDeliveredQty), 0);
            $diff = $req_qty - $current_stock;

            $values[$key]['total'] = $req_qty;
            $values[$key]['current_stock'][] = $current_stock;
            $values[$key]['productionable'][] = max($diff, 0);

            if (($req_qty - $current_stock) == 0) {
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

    private function loadRequisitionForDisplay($id): Requisition
    {
        return Requisition::query()
            ->with([
                'fromStore:id,name',
                'toStore:id,name',
                'outlet:id,name,address',
                'createdBy:id,name,email',
                'approvedBy:id,name,email',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));
    }

    private function deliveredQuantitiesByCoi(Requisition $requisition)
    {
        return RequisitionDeliveryItem::query()
            ->where('requisition_id', $requisition->id)
            ->select('coi_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('coi_id')
            ->pluck('qty', 'coi_id');
    }
}
