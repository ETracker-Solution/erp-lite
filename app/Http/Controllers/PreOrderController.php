<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePreOrderRequest;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\Outlet;
use App\Models\PreOrder;
use App\Models\Purchase;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'supplier_groups' => SupplierGroup::all(),
            'suppliers' => Supplier::all(),
            'customers' => Customer::all(),
            'stores' => Store::where(['type' => 'RM', 'doc_type' => 'ho', 'doc_id' => null])->get(),
            'outlets' => Outlet::where(['status' => 'active'])->get(),
            'uid' => 12,

        ];

        return view('pre_order.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePreOrderRequest $request)
    {
        $data=$request->validated();
        dd($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }
}
