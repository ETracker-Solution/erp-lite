<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Purchase;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\PurchaseItem;
use App\Models\Requisition;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Models\SupplierTransaction;
use App\Models\Transaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;


class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $purchases = Purchase::with('supplier', 'store')->where('type','rm')->latest();
            return DataTables::of($purchases)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('purchase.action', compact('row'));
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
        return view('purchase.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serial_no = null;
        if (!auth()->user()->is_super && \auth()->user()->employee->user_of != 'ho') {
            $doc_id = \auth()->user()->employee->outlet_id ?? \auth()->user()->employee->factory_id;
            $doc_type = \auth()->user()->employee->outlet_id ? 'outlet' : 'factory';
            $user_store = Store::where(['doc_type' => $doc_type, 'doc_id' => $doc_id])->first();
            $outlet_id = $user_store->doc_id;
        }
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'supplier_groups' => SupplierGroup::where('status', 'active')->get(),
            'suppliers' => Supplier::all(),
            'stores' => Store::where(['type' => 'RM', 'doc_type' => 'ho', 'doc_id' => null])->get(),
            'uid' => generateUniqueCode(Purchase::class, 'uid'),

        ];

        return view('purchase.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (count($validated['products']) < 1) {
                Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
                return back();
            }

            $purchase = Purchase::query()->create($validated);
            $purchase->amount = $purchase->net_payable;

            foreach ($validated['products'] as $product) {
                $qty = $product['quantity'];

                if (isset($product['alter_unit']) && $product['alter_unit']) {
                    $total_rate = $product['rate'] * $product['quantity'];
                    $product['quantity'] = ($product['a_unit_quantity'] > 0 ? $product['a_unit_quantity'] : 1) * $product['quantity'];
                    $product['rate'] = $total_rate / $product['quantity'];
                }

                $existingItem = PurchaseItem::where('coi_id', $product['coi_id'])->latest()->first();

                $rate = isset($existingItem) ? $existingItem->rate : 0;

                if ($rate === null || $rate === 0) {
                    $rate = $product['rate'];
                }

                $purchase->items()->create([
                    'coi_id' => $product['coi_id'],
                    'quantity' => $product['quantity'],
                    'value_amount' => $product['value_amount'],
                    'a_unit_quantity' => $product['a_unit_quantity'],
                    'alter_unit_id' => $product['alter_unit_id'],
                    'alt_unit_rate' => $product['value_amount'] / $product['quantity'],
                    'unit_qty' => $qty,
                    'converted_unit_qty' => $product['converted_unit_qty'],
                    'company_id' => auth()->user()->company_id,
                    'purchase_id' => $purchase->id,
                    'rate' => $rate,
                ]);
            
                InventoryTransaction::query()->create([
                    'store_id' => $purchase->store_id,
                    'doc_type' => 'GPB',
                    'doc_id' => $purchase->id,
                    'quantity' => $product['quantity'] ?? $product['a_unit_quantity'],
                    'rate' => $product['rate'],
                    'amount' => ($product['quantity'] ?? $product['a_unit_quantity']) * $product['rate'],
                    'date' => $purchase->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }
            



            addAccountsTransaction('GPB', $purchase, 15, 22);

            // Supplier Transaction Effect
            SupplierTransaction::query()->create([
                'supplier_id' => $purchase->supplier_id,
                'doc_type' => 'GPB',
                'doc_id' => $purchase->id,
                'amount' => $purchase->net_payable,
                'date' => $purchase->date,
                'transaction_type' => 1,
                'chart_of_account_id' => 22,
                'description' => 'Purchase of goods',
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Purchase Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('purchases.index');

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = [
            'model' => Purchase::findOrFail(decrypt($id)),

        ];

        return view('purchase.show', $data);
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
        return view('purchase.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $purchase->update($validated);
            PurchaseItem::where('purchase_id', $purchase->id)->delete();
            InventoryTransaction::where(['doc_id' => $purchase->id, 'doc_type' => 'GPB'])->delete();
            $purchase->amount = $purchase->net_payable;
            foreach ($validated['products'] as $product) {

                if ($product['alter_unit']) {
                    $total_rate = $product['rate'] * $product['quantity'];
                    $product['quantity'] = ($product['a_unit_quantity'] > 0 ? $product['a_unit_quantity'] : 1) * $product['quantity'];
                    $product['rate'] = $total_rate / $product['quantity'];
                }

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
            addAccountsTransaction('GPB', $purchase, 13, 12);

            // Supplier Transaction Effect
            SupplierTransaction::where(['doc_id' => $purchase->id, 'doc_type' => 'GPB'])->delete();
            SupplierTransaction::query()->create([
                'supplier_id' => $purchase->supplier_id,
                'doc_type' => 'GPB',
                'doc_id' => $purchase->id,
                'amount' => $purchase->net_payable,
                'date' => $purchase->date,
                'transaction_type' => 1,
                'chart_of_account_id' => 12,
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
        return redirect()->route('purchases.index');
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
        Toastr::success('Purchase Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('purchases.index');
    }

    public function purchaseCancel($id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::findOrFail(decrypt($id));
            $purchase->status = 'cancelled';
            $purchase->save();
            AccountTransaction::where('doc_type', 'GPB')->where('doc_id', $purchase->id)->delete();
            InventoryTransaction::where('doc_type', 'GPB')->where('doc_id', $purchase->id)->delete();
            SupplierTransaction::where(['doc_type' => 'GPB', 'doc_id' => $purchase->id])->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Purchase Cancelled Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('purchases.index');
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
        $data = [
            'model' => Purchase::find(decrypt($id)),

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
}
