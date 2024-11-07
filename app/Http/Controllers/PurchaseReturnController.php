<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Http\Requests\StorePurchaseReturnRequest;
use App\Http\Requests\UpdatePurchaseReturnRequest;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Models\SupplierTransaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $purchase_returns = PurchaseReturn::with('supplier', 'store')->latest();
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
            'supplier_groups' => SupplierGroup::all(),
            'suppliers' => Supplier::all(),
            'stores' => Store::where(['type' => 'RM', 'doc_type' => 'ho', 'doc_id' => null])->get(),
            'uid' => getNextId(PurchaseReturn::class),
            'purchases' => Purchase::where('status', '!=', 'returned')->get(),
        ];
        return view('purchase_return.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseReturnRequest $request)
    {
        $data = $request->validated();
//        DB::beginTransaction();
//        try {
        if (count($data['products']) < 1) {
            Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
            return back();
        }
        $purchase_return = PurchaseReturn::query()->create($data);
        Purchase::where('id', $data['purchase_id'])->update(['status' => 'returned']);
        $purchase_return->amount = $purchase_return->net_payable;
        foreach ($data['products'] as $product) {
            $purchase_return->items()->create($product);
            // Inventory Transaction Effect
            InventoryTransaction::query()->create([
                'store_id' => $purchase_return->store_id,
                'doc_type' => 'GPBR',
                'doc_id' => $purchase_return->id,
                'quantity' => $product['quantity'],
                'rate' => $product['rate'],
                'amount' => $product['quantity'] * $product['rate'],
                'date' => $purchase_return->date,
                'type' => -1,
                'coi_id' => $product['coi_id'],
            ]);
        }


        // Accounts Transaction Effect

        addAccountsTransaction('GPB', $purchase_return, 22, 15);

        // Supplier Transaction Effect
        SupplierTransaction::query()->create([
            'supplier_id' => $purchase_return->supplier_id,
            'doc_type' => 'GPB',
            'doc_id' => $purchase_return->id,
            'amount' => $purchase_return->net_payable,
            'date' => $purchase_return->date,
            'transaction_type' => -1,
            'chart_of_account_id' => 22,
            'description' => 'Purchase of goods',
        ]);
//            DB::commit();
//        } catch (\Exception $exception) {
//            DB::rollBack();
//            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
//            return back();
//        }
        Toastr::success('Purchase Return Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('purchase-returns.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = [
            'model' => PurchaseReturn::findOrFail(decrypt($id)),
        ];
        return view('purchase_return.show', $data);
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

    public function pdfDownload($id)
    {
        $data = [
            'model' => PurchaseReturn::findOrFail(decrypt($id)),
        ];

        $pdf = PDF::loadView(
            'purchase_return.pdf',
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
