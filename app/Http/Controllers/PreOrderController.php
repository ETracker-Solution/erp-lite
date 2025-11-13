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
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $outlet_id = false;
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
        $outletStores = Store::where(['doc_type' => 'outlet', 'type' => 'FG'])->get();
        if (auth()->user()->employee && auth()->user()->employee->outlet_id) {
            $outletStores = Store::where(['doc_type' => 'outlet', 'type' => 'FG', 'doc_id' => auth()->user()->employee->outlet_id])->get();
        }
        return view('pre_order.index', compact('outlets', 'factoryStores', 'outletStores'));
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
        $preOrder->update($request->only(['size', 'flavour', 'cake_message', 'remark','delivery_date','delivery_time']));
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
                if (!$request->factory_store) {
                    Toastr::error('Select A Store');
                    return back();
                }
                $store = Store::find($request->factory_store);
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
                $production = Production::query()->create($productionData);

                $totalRate = 0;
                $totalQty = 0;
                foreach ($req->items as $item) {
                    $qty = $item->quantity;
                    $rate = $item->unit_price;
                    $amount = $qty * $rate;
                    $totalRate += $amount;
                    $totalQty += $qty;
                    $itemData = [
                        'coi_id' => $item->coi_id,
                        'rate' => $rate,
                        'quantity' => $qty,
                    ];
                    $production->items()->create($itemData);
                    // Inventory Transaction Effect
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
