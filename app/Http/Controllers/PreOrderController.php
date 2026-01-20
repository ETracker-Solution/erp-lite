<?php

namespace App\Http\Controllers;

use App\Classes\PreOrderNumber;
use App\Http\Requests\StorePreOrderRequest;
use App\Models\AccountTransaction;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\InventoryTransaction;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\PreOrder;
use App\Models\PreOrderItem;
use App\Models\Production;
use App\Models\ProductionRecipe;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use App\Services\ExportService;

class PreOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }


    public function exportPreOrder($type)
    {
        if (auth()->user()->employee && auth()->user()->employee->outlet_id) {
            $outlet_id = auth()->user()->employee->outlet_id;
            $preOrderItem = PreOrderItem::with('coi.parent', 'preOrder.outlet', 'preOrder.customer')
                ->latest();
        } else {
            if (\request()->filled('outlet_id')) {
                $outlet_id = \request()->outlet_id;
            }
            $preOrderItem = PreOrderItem::with('coi.parent', 'preOrder.outlet', 'preOrder.customer')->latest();
        }

        if ($outlet_id) {
            $preOrderItem = $preOrderItem->whereHas('preOrder', function ($query) use ($outlet_id) {
                $query->where('delivery_point_id', $outlet_id);
            });
        }

        if (\request()->filled('status')) {
            $status = \request()->status;
            $preOrderItem = $preOrderItem->whereHas('preOrder', function ($query) use ($status) {
                $query->where('status', $status);
            });
        }

        if (\request()->filled('filter_by') && \request()->filled('from_date') && \request()->filled('to_date')) {
            $column = \request()->filter_by;
            $from_date = Carbon::parse(request()->from_date)->format('Y-m-d');
            $to_date = Carbon::parse(request()->to_date)->format('Y-m-d');

            $preOrderItem = $preOrderItem->whereHas('preOrder', function ($query) use ($column, $from_date, $to_date) {
                $query->whereDate($column, '>=', $from_date)
                    ->whereDate($column, '<=', $to_date);
            });
        }

        $exportableData = [
            'preOrderItems' => $preOrderItem->get()
        ];

        $viewFileName = 'pre_orders';
        $filenameToDownload = date('ymdHis') . '_pre_orders';
        return $this->exportService->exportFile($type, $viewFileName, $exportableData, $filenameToDownload, 'L'); // L stands for Landscape, if Portrait needed, just remove this params

    }

    public function index()
    {
        if (\request()->ajax()) {
            $pre_orders = $this->getFilteredData();
            return DataTables::of($pre_orders)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $stock_status = true;
                    $missing_rm_list = [];
                    if ($row->status == 'approved' && $row->approvalInfo) {
                        $rm_store_id = $row->approvalInfo->rm_store_id;
                        if ($rm_store_id) {
                            $required_rm = [];
                            foreach ($row->items as $item) {
                                $recipes = \App\Models\ProductionRecipe::where('fg_id', $item->coi_id)->get();
                                if ($recipes->isEmpty()) {
                                    $is_custom = \App\Models\ChartOfInventory::find($item->coi_id);
                                    if ($is_custom && $is_custom->price == 0 && strlen($is_custom->name) > 11) {
                                        $newString = substr($is_custom->name, 0, -13);
                                        $original_product = \App\Models\ChartOfInventory::where('name', $newString)->where('parent_id', $is_custom->parent_id)->first();
                                        if ($original_product) {
                                            $recipes = \App\Models\ProductionRecipe::where('fg_id', $original_product->id)->get();
                                        }
                                    }
                                }
                                foreach ($recipes as $recipe) {
                                    $rm_id = $recipe->rm_id;
                                    $qty = $recipe->qty * $item->quantity;
                                    if (isset($required_rm[$rm_id])) {
                                        $required_rm[$rm_id]['qty'] += $qty;
                                    } else {
                                        $required_rm[$rm_id] = [
                                            'qty' => $qty,
                                            'name' => $recipe->coi->name ?? 'Unknown RM'
                                        ];
                                    }
                                }
                            }
                            // Additional RM
                            $addRmIds = json_decode($row->approvalInfo->rm_ids);
                            $addQuantities = json_decode($row->approvalInfo->quantities);
                            if ($addRmIds && count($addRmIds) > 0) {
                                foreach ($addRmIds as $idx => $rmId) {
                                    if (!isset($addQuantities[$idx])) continue;
                                    $qty = $addQuantities[$idx];
                                    if (isset($required_rm[$rmId])) {
                                        $required_rm[$rmId]['qty'] += $qty;
                                    } else {
                                        $rmName = \App\Models\ChartOfInventory::find($rmId)->name ?? 'Unknown RM';
                                        $required_rm[$rmId] = [
                                            'qty' => $qty,
                                            'name' => $rmName
                                        ];
                                    }
                                }
                            }
                            // Check stock
                            foreach ($required_rm as $rmId => $data) {
                                $currentStock = availableInventoryBalance($rmId, $rm_store_id);
                                if ($currentStock < $data['qty']) {
                                    $stock_status = false;
                                    $missing_rm_list[] = $data['name'] . " (Req: " . $data['qty'] . ", Avail: " . $currentStock . ")";
                                }
                            }
                        }
                    }
                    $row->stock_status = $stock_status;
                    $row->missing_rm_list = $missing_rm_list;
                    return view('pre_order.action', compact('row'));
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
        $outlets = Outlet::where('status', 'active')->get();
        $factoryStores = Store::where(['doc_type' => 'factory', 'type' => 'FG'])->get();
        $rmStores = Store::where(['type' => 'RM'])->get();
        $rawMaterials = ChartOfInventory::with('unit')->where(['type' => 'item', 'rootAccountType' => 'RM'])->get();
        $outletStores = Store::where(['doc_type' => 'outlet', 'type' => 'FG'])->get();
        if (auth()->user()->employee && auth()->user()->employee->outlet_id) {
            $outletStores = Store::where(['doc_type' => 'outlet', 'type' => 'FG', 'doc_id' => auth()->user()->employee->outlet_id])->get();
            $rmStores = [];
        }
        if (auth()->user()->employee && auth()->user()->employee->factory_id) {
            $rmStores = Store::where(['doc_type' => 'factory', 'type' => 'RM'])->get();
        }
        return view('pre_order.index', compact('outlets', 'factoryStores', 'outletStores', 'rmStores', 'rawMaterials'));
    }

    protected function getFilteredData()
    {
        $orders = PreOrder::with(['customer', 'outlet', 'deliveryPoint'])
            ->leftJoin('others_outlet_sales', 'others_outlet_sales.invoice_number', '=', 'pre_orders.order_number')
            ->select('pre_orders.*')
            ->selectRaw('
        CASE
            WHEN others_outlet_sales.id IS NOT NULL THEN
                GREATEST(
                    pre_orders.grand_total - (
                        pre_orders.advance_amount +
                        COALESCE(others_outlet_sales.delivery_point_receive_amount, 0)
                    ),
                    0
                )
            ELSE NULL
        END as due_amount
    ')
            ->distinct('pre_orders.id')   // 👈 prevents duplicate rows
            ->latest();

        if (auth()->user()->employee && auth()->user()->employee->outlet_id) {
            $orders->where('pre_orders.delivery_point_id', auth()->user()->employee->outlet_id);
        }

        if (request()->filled('outlet_id')) {
            $orders->where('pre_orders.delivery_point_id', request()->outlet_id);
        }

        if (request()->filled('status')) {
            $orders->where('pre_orders.status', request()->status);
        }

        if (request()->filled('filter_by') && request()->filled('from_date') && request()->filled('to_date')) {
            $column = request()->filter_by;
            $from_date = Carbon::parse(request()->from_date)->format('Y-m-d');
            $to_date = Carbon::parse(request()->to_date)->format('Y-m-d');
            $orders->whereDate($column, '>=', $from_date)
                ->whereDate($column, '<=', $to_date);
        }

        return $orders;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'supplier_groups' => SupplierGroup::all(),
            'suppliers' => Supplier::all(),
            'customers' => Customer::all(),
            'stores' => Store::where(['type' => 'RM', 'doc_type' => 'ho', 'doc_id' => null])->get(),
            'outlets' => Outlet::where(['status' => 'active'])->get(),
            // 'outlets' => Outlet::where(['status' => 'active'])->get(),
            'uid' => PreOrderNumber::serial_number(),

        ];

        return view('pre_order.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePreOrderRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (count($validated['products']) < 1) {
                Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
                return back();
            }
            $filename = '';
            if ($request->hasfile('image')) {
                $file = $request->file('image');
                $filename = date('Ymdmhs') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('/upload'), $filename);
            }
            $validated['image'] = $filename ?? null;

            $pre_order = PreOrder::query()->create($validated);
            $pre_order->amount = $pre_order->net_payable;
            foreach ($validated['products'] as $product) {
                $pre_order->items()->create($product);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Pre Order Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('pre-orders.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(PreOrder $preOrder)
    {
        $data = [
            'model' => $preOrder,
        ];

        return view('pre_order.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $preOrder = PreOrder::findOrFail(decrypt($id));
        $data = [
            'model' => $preOrder,
        ];

        return view('pre_order.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $preOrder = PreOrder::findOrFail(decrypt($id));
        $preOrder->update($request->only(['size', 'flavour', 'cake_message', 'remark', 'delivery_date', 'delivery_time']));
        Toastr::success('Pre Order Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            PreOrder::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Pre Order Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('pre-orders.index');
    }

    public function pdf($id)
    {
        $data = [
            'model' => PreOrder::find($id),

        ];
//        return view( 'pre_order.pdf',$data);
        $pdf = PDF::loadView('pre_order.pdf', $data);

        $name = \Carbon\Carbon::now()->format('d-m-Y');
        return $pdf->stream($name . '.pdf');
    }

    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $req = PreOrder::findOrFail($id);
            $products = $req->items;
            $productIds = $req->items()->pluck('coi_id')->toArray();
            $updatableData = [
                'status' => $request->status,
            ];
            if ($request->status == 'approved') {
                if (!$request->factory_store) {
                    Toastr::error('Select FG Store Please');
                    return back();
                }
                if (!$request->rm_store) {
                    Toastr::error('Select RM Store Please');
                    return back();
                }

                $req->approvalInfo()->create([
                    'fg_store_id' => $request->factory_store,
                    'rm_store_id' => $request->rm_store,
                    'additionalData' => null,
                    'rm_ids' => $request->input('rm_ids') && count($request->input('rm_ids')) > 0 ? json_encode($request->input('rm_ids')) : null,
                    'quantities' => $request->input('quantities') && count($request->input('quantities')) > 0 ? json_encode($request->input('quantities')) : null,
                    'created_by' => auth()->user()->id
                ]);
            }
            if ($request->status == 'delivered') {
                if (!$request->factory_store) {
                    Toastr::error('Select A Store');
                    return back();
                }
                $store = Store::find($request->factory_store);

                $storeStocks = fetchStoreProductBalances($productIds, [$request->factory_store]);
                foreach ($products as $product) {
                    $current_stock = count($storeStocks) > 0 && isset($storeStocks[$store->id][$product->coi_id]) ? $storeStocks[$store->id][$product->coi_id] : 0;

                    $deliveredQty = $product->coi->requisitionDeliveryItems()->whereHas('requisitionDelivery', function ($q) {
                        return $q->where('status', 'completed');
                    })->sum('quantity');

                    $preOrderDeliveredQty = $product->coi->preOrderItems()->whereHas('preOrder', function ($q) {
                        return $q->where('status', 'delivered');
                    })->sum('quantity');

                    $current_stock = max(($current_stock - $deliveredQty - $preOrderDeliveredQty), 0);
                    if ($current_stock < $product->quantity) {
                        DB::rollBack();
                        Toastr::error($product->coi->name . ' is not available');
                        return back();
                    }
                }
                $updatableData = [
                    'status' => $request->status,
                    'factory_delivery_store_id' => $request->factory_store
                ];
            }
            if ($request->status == 'received') {

                if (!$request->outlet_store) {
                    Toastr::error('Select A Store');
                    return back();
                }

                $updatableData = [
                    'status' => $request->status,
                    'outlet_receive_store_id' => $request->outlet_store
                ];

                foreach ($products as $product) {
                    InventoryTransaction::query()->create([
                        'store_id' => $req->factory_delivery_store_id,
                        'doc_type' => 'PO',
                        'doc_id' => $req->id,
                        'quantity' => $product->quantity,
                        'rate' => $product->unit_price,
                        'amount' => $product->quantity * $product->unit_price,
                        'date' => date('y-m-d'),
                        'type' => -1,
                        'coi_id' => $product->coi_id,
                    ]);
                    InventoryTransaction::query()->create([
                        'store_id' => $request->outlet_store,
                        'doc_type' => 'PO',
                        'doc_id' => $req->id,
                        'quantity' => $product->quantity,
                        'rate' => $product->unit_price,
                        'amount' => $product->quantity * $product->unit_price,
                        'date' => date('y-m-d'),
                        'type' => 1,
                        'coi_id' => $product->coi_id,
                    ]);
                }
            }

            if ($request->status == 'cancelled') {
                $sale_of_pre_order = $req->sale;
                $delivery_of_pre_order = OthersOutletSale::where('invoice_number', $sale_of_pre_order->invoice_number)->first();
                $customer = $sale_of_pre_order->customer;
                $membership = $customer->membership;
                $membershipPointHistory = $customer->membershipPointHistories()->where('sale_id', $sale_of_pre_order->id)->first();
                AccountTransaction::where([
                    'doc_type' => 'POS',
                    'doc_id' => $sale_of_pre_order->id,
                ])->delete();
                if ($membershipPointHistory) {
                    $membership->decrement('point', $membershipPointHistory->point);
                    $membershipPointHistory->delete();
                }
                if ($delivery_of_pre_order) {
                    $delivery_of_pre_order->delete();
                }
                $sale_of_pre_order->delete();
            }

            if ($request->status == 'ready_to_delivery') {
                $approvalData = $req->approvalInfo;
                if (!$approvalData->fg_store_id) {
                    Toastr::error('FG Store Not Set');
                    return back();
                }
                if (!$approvalData->rm_store_id) {
                    Toastr::error('RM Store Not Set');
                    return back();
                }
                $store = Store::find($approvalData->fg_store_id);
                $rm_store = Store::find($approvalData->rm_store_id);
                $uid = generateUniqueUUID($store->doc_id, Production::class, 'uid', $store->doc_type == 'factory');
                $productionData = [
                    'uid' => $uid,
                    'store_id' => $store->id,
                    'factory_id' => $store->doc_id,
                    'date' => date('Y-m-d'),
                    'status' => 'completed',
                    'remark' => 'Auto Production From Pre Order',
                    'subtotal' => 0,
                    'total_quantity' => 0,
                    'created_by' => auth()->user()->id,
                ];

                $missing_items = [];
                $totalRate = 0;
                $totalQty = 0;
                $required_rm = [];

                foreach ($req->items as $item) {
                    $qty = $item->quantity;
                    $rate = $item->unit_price;
                    $amount = $qty * $rate;
                    $totalRate += $amount;
                    $totalQty += $qty;

                    $is_custom = ChartOfInventory::find($item->coi_id);
                    $recipes_items = collect();
                    if ($is_custom->price == 0) {
                        if (strlen($is_custom->name) > 11) {
                            $newString = substr($is_custom->name, 0, -13);
                            $original_product = ChartOfInventory::where('name', $newString)->where('parent_id', $is_custom->parent_id)->first();
                            if ($original_product) {
                                $recipes_items = ProductionRecipe::where('fg_id', $original_product->id)->get();
                            }
                        }
                    } else {
                        $recipes_items = ProductionRecipe::where('fg_id', $item->coi_id)->get();
                    }

                    foreach ($recipes_items as $recipe_item) {
                        $rm_id = $recipe_item->rm_id;
                        $rm_qty = $recipe_item->qty * $qty;
                        if (isset($required_rm[$rm_id])) {
                            $required_rm[$rm_id]['qty'] += $rm_qty;
                        } else {
                            $required_rm[$rm_id] = [
                                'qty' => $rm_qty,
                                'name' => $recipe_item->coi->name ?? 'Unknown RM'
                            ];
                        }
                    }
                }

                $rmIds = json_decode($approvalData->rm_ids);
                $quantities = json_decode($approvalData->quantities);

                if ($rmIds && count($rmIds) > 0) {
                    foreach ($rmIds as $index => $rmId) {
                        if (!isset($quantities[$index])) continue;
                        $quantity = $quantities[$index];
                        if (isset($required_rm[$rmId])) {
                            $required_rm[$rmId]['qty'] += $quantity;
                        } else {
                            $rmName = ChartOfInventory::find($rmId)->name ?? 'Unknown RM';
                            $required_rm[$rmId] = [
                                'qty' => $quantity,
                                'name' => $rmName
                            ];
                        }
                    }
                }

                // Verify stock availability
                foreach ($required_rm as $rmId => $data) {
                    $currentStock = availableInventoryBalance($rmId, $rm_store->id);
                    if ($currentStock < $data['qty']) {
                        $missing_items[] = "RM: " . $data['name'] . " (Required: " . $data['qty'] . ", Available: " . $currentStock . ")";
                    }
                }

                if (!empty($missing_items)) {
                    DB::rollBack();
                    $errorMsgs = implode('<br>', $missing_items);
                    Toastr::error("Stock Insufficient:<br>$errorMsgs", 'Error', ["progressBar" => true, "timeOut" => 10000]);
                    return back();
                }

                // If all good, proceed with production
                $production = Production::query()->create($productionData);
                foreach ($req->items as $item) {
                    $qty = $item->quantity;
                    $rate = $item->unit_price;
                    $amount = $qty * $rate;
                    $itemData = [
                        'coi_id' => $item->coi_id,
                        'rate' => $rate,
                        'quantity' => $qty,
                    ];
                    $prodItem = $production->items()->create($itemData);
                    
                    // FG Production Inventory Transaction
                    InventoryTransaction::query()->create([
                        'store_id' => $production->store_id,
                        'doc_type' => 'FGP',
                        'doc_id' => $production->id,
                        'quantity' => $qty,
                        'rate' => $rate,
                        'amount' => $amount,
                        'date' => $production->date,
                        'type' => 1,
                        'coi_id' => $item->coi_id,
                    ]);

                    // RM Consumptions for this item
                    $is_custom = ChartOfInventory::find($item->coi_id);
                    $recipes_items = collect();
                    if ($is_custom->price == 0 && strlen($is_custom->name) > 11) {
                        $newString = substr($is_custom->name, 0, -13);
                        $original_product = ChartOfInventory::where('name', $newString)->where('parent_id', $is_custom->parent_id)->first();
                        if ($original_product) {
                            $recipes_items = ProductionRecipe::where('fg_id', $original_product->id)->get();
                        }
                    } else {
                        $recipes_items = ProductionRecipe::where('fg_id', $item->coi_id)->get();
                    }

                    foreach ($recipes_items as $recipe_item) {
                        $rm = new stdClass();
                        $rm->date = date('Y-m-d');
                        $rm->coi_id = $recipe_item->rm_id;
                        $rm->rate = 0;
                        $rm->amount = 0;
                        $rm->store_id = $rm_store->id;
                        $rm->quantity = $recipe_item->qty * $qty;
                        $rm->id = $prodItem->id;
                        addInventoryTransaction(-1, 'PO', (object)$rm);
                    }
                }

                // Add Additional RM consumption
                if ($rmIds && count($rmIds) > 0) {
                    foreach ($rmIds as $index => $rmId) {
                        if (!isset($quantities[$index])) continue;
                        $rm = new stdClass();
                        $rm->date = date('Y-m-d');
                        $rm->coi_id = $rmId;
                        $rm->rate = 0;
                        $rm->amount = 0;
                        $rm->store_id = $rm_store->id;
                        $rm->quantity = $quantities[$index];
                        $rm->id = $production->items()->first()->id;
                        addInventoryTransaction(-1, 'PO', (object)$rm);
                    }
                }

                $production->update([
                    'subtotal' => $totalRate,
                    'total_quantity' => $totalQty,
                ]);
            }

            $req->update($updatableData);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
        }
        Toastr::success('Pre Order Status Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('pre-orders.index');
    }
}
