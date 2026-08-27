<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\AccountTransaction;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Models\SupplierTransaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Yajra\DataTables\Facades\DataTables;

class FGPurchaseController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = Purchase::query()
                ->select([
                    'purchases.id',
                    'purchases.uid',
                    'purchases.date',
                    'purchases.status',
                    'purchases.subtotal',
                    'purchases.vat',
                    'purchases.net_payable',
                    'purchases.supplier_id',
                    'purchases.store_id',
                    'purchases.created_at',
                ])
                ->with([
                    'supplier:id,name',
                    'store:id,name',
                ])
                ->where('type', 'fg')
                ->latest('id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('subtotal', fn ($row) => number_format((float) $row->subtotal, 2))
                ->editColumn('vat', fn ($row) => number_format((float) $row->vat, 2))
                ->editColumn('net_payable', fn ($row) => number_format((float) $row->net_payable, 2))
                ->addColumn('action', function ($row) {
                    return view('fg_purchase.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }

        return view('fg_purchase.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fg_purchase.create', [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'FG'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'supplier_groups' => SupplierGroup::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name']),
            'stores' => Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'factory'])
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        $validated = $request->validated();
        $validated['type']='fg';
        DB::beginTransaction();
        try {
            if (count($validated['products']) < 1) {
                Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
                return back();
            }
            $purchase = Purchase::query()->create($validated);
            $purchase->amount = $purchase->net_payable;
            foreach ($validated['products'] as $product) {
                $purchase->items()->create($product);
                // Inventory Transaction Effect
                InventoryTransaction::query()->create([
                    'store_id' => $purchase->store_id,
                    'doc_type' => 'GPB',
                    'doc_id' => $purchase->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $purchase->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }


            // Accounts Transaction Effect

            addAccountsTransaction('GPB', $purchase, getFGInventoryGLId(), getAccountsPayableGLId());

            // Supplier Transaction Effect
            SupplierTransaction::query()->create([
                'supplier_id' => $purchase->supplier_id,
                'doc_type' => 'GPB',
                'doc_id' => $purchase->id,
                'amount' => $purchase->net_payable,
                'date' => $purchase->date,
                'transaction_type' => 1,
                'chart_of_account_id' => getAccountsPayableGLId(),
                'description' => 'Purchase of goods',
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Purchase Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('fg-purchases.index');

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $purchase = Purchase::query()
            ->with([
                'supplier:id,name,address,mobile',
                'store:id,name',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        return view('fg_purchase.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'supplier_groups' => SupplierGroup::all(),
            'suppliers' => Supplier::all(),
            'stores' => Store::where(['type' => 'RM'])->get(),
            'purchase' => Purchase::with('supplier')->find(decrypt($id))
        ];
        return view('fg_purchase.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $validated['type'] = 'fg';
            $purchase->update($validated);
            PurchaseItem::where('purchase_id', $purchase->id)->delete();
            InventoryTransaction::where(['doc_id' => $purchase->id, 'doc_type' => 'GPB'])->delete();
            $purchase->amount = $purchase->net_payable;
            foreach ($validated['products'] as $product) {
                $purchase->items()->create($product);

                // Inventory Transaction Effect
                InventoryTransaction::query()->create([
                    'store_id' => $purchase->store_id,
                    'doc_type' => 'GPB',
                    'doc_id' => $purchase->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $purchase->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            // Accounts Transaction Effect
            AccountTransaction::where(['doc_id' => $purchase->id, 'doc_type' => 'GPB'])->delete();
            addAccountsTransaction('GPB', $purchase, getFGInventoryGLId(), getAccountsPayableGLId());

            // Supplier Transaction Effect
            SupplierTransaction::where(['doc_id' => $purchase->id, 'doc_type' => 'GPB'])->delete();
            SupplierTransaction::query()->create([
                'supplier_id' => $purchase->supplier_id,
                'doc_type' => 'GPB',
                'doc_id' => $purchase->id,
                'amount' => $purchase->net_payable,
                'date' => $purchase->date,
                'transaction_type' => 1,
                'chart_of_account_id' => getAccountsPayableGLId(),
                'description' => 'Purchase of goods',
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('Goods Purchase Updated Successful!.', '', ["progressbar" => true]);
        return redirect()->route('fg-purchases.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            AccountTransaction::where('doc_type', 'GPB')->where('doc_id', decrypt($id))->delete();
            InventoryTransaction::where('doc_type', 'GPB')->where('doc_id', decrypt($id))->delete();
            SupplierTransaction::where(['doc_type' => 'GPB', 'doc_id' => decrypt($id)])->delete();
            Purchase::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Purchase Deleted Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('fg-purchases.index');
    }

    public function print($id)
    {


        $data = [
            'model' => Purchase::find($id),
        ];

        return view('admin.purchase.print', $data);
    }

    public function pdf($id)
    {
        $data = [
            'model' => Purchase::find($id),

        ];

        $pdf = PDF::loadView(
            'purchase.pdf',
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

    public function pdfDownload($id)
    {
        $purchase = Purchase::query()
            ->with([
                'supplier:id,name,address,mobile',
                'store:id,name',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        $pdf = PDF::loadView(
            'fg_purchase.pdf',
            ['model' => $purchase],
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
