<?php

namespace App\Http\Controllers;

use App\Classes\PreOrderNumber;
use App\Http\Requests\StorePreOrderRequest;
use App\Models\ChartOfInventory;
use App\Models\Customer;
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
            $pre_orders = PreOrder::with('customer', 'outlet')->latest();
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
        return view('pre_order.index');
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

            $purchase = PreOrder::query()->create($validated);
            $purchase->amount = $purchase->net_payable;
            foreach ($validated['products'] as $product) {
                $purchase->items()->create($product);
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
}
