<?php

namespace App\Http\Controllers;

use App\Classes\PreOrderNumber;
use App\Http\Requests\StorePreOrderRequest;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\InventoryTransaction;
use App\Models\Outlet;
use App\Models\PreOrder;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class PreOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        if (auth()->user()->employee && auth()->user()->employee->outlet_id) {
            $outlet_id = auth()->user()->employee->outlet_id;
            $orders = PreOrder::with('customer', 'outlet', 'deliveryPoint')->where('delivery_point_id', $outlet_id)->orWhere('outlet_id', $outlet_id)->latest();
        } else {
            $orders = PreOrder::with('customer', 'outlet', 'deliveryPoint')->latest();
        }
        if (\request()->filled('outlet_id')) {
            $orders->where('delivery_point_id', \request()->outlet_id);
        }
        if (\request()->filled('status')) {
            $orders->where('status', \request()->status);
        }
        if (\request()->filled('filter_by')) {
            if (\request()->filter_by == 'delivery_today') {
                $orders->whereDate('delivery_date', now()->format('Y-m-d'));
            }
            if (\request()->filter_by == 'order_today') {
                $orders->whereDate('created_at', now()->format('Y-m-d'));
            }

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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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

        $pdf = PDF::loadView(
            'pre_order.pdf',
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

    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $req = PreOrder::findOrFail($id);
            $updatableData = [
                'status' => $request->status,
            ];
            if ($request->status == 'delivered') {
                $updatableData = [
                    'status' => $request->status,
                    'factory_delivery_store_id' => $request->factory_store
                ];
            }
            if ($request->status == 'received') {
                $updatableData = [
                    'status' => $request->status,
                    'outlet_receive_store_id' => $request->outlet_store
                ];
            }
            $req->update($updatableData);

            $products = $req->items;

            foreach ($products as $product){
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
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return $exception->getMessage();
        }
        Toastr::success('Pre Order Status Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('pre-orders.index');
    }
}
