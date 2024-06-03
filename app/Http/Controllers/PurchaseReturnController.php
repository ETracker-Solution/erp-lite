<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Http\Requests\StorePurchaseReturnRequest;
use App\Http\Requests\UpdatePurchaseReturnRequest;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchase_returns = PurchaseReturn::all();
        if (\request()->ajax()) {
            return DataTables::of($purchase_returns)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('purchase_return.action', compact('row'));
                })->addColumn('purchase_info', function ($row) {
                    $data = [
                        'Supplier' => $row->purchase->supplier->name ?? "",
                        'Purchase No' => $row->purchase->purchase_number ?? "",
                        'Challan No' => $row->purchase->challan_no ?? "",
                    ];
                    return view('common.flexible', compact('data'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->rawColumns(['status', 'action', 'purchase_info'])
                ->make(true);
        }
        return view('purchase_return.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [
            'purchases' => Purchase::all()
        ];
        return view('purchase_return.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseReturnRequest $request)
    {
        dd($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseReturn $purchaseReturn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseReturn $purchaseReturn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseReturnRequest $request, PurchaseReturn $purchaseReturn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseReturn $purchaseReturn)
    {
        //
    }
}
