<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\Product;
use App\Models\Purchase;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\PurchaseItem;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Models\SupplierTransaction;
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
        $purchases = Purchase::latest();
        if (\request()->ajax()) {
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
        $serial_count = Purchase::latest()->first() ? Purchase::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'supplier_groups' => SupplierGroup::all(),
            'suppliers' => Supplier::all(),
            'stores' => Store::where(['type' => 'RM'])->get(),
            'serial_no' => $serial_no,

        ];

        return view('purchase.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        $validated = $request->validated();
//        DB::beginTransaction();
//        try {
        if (count($validated['products']) < 1) {
            Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
            return back();
        }
        $purchase = Purchase::create($validated);
        foreach ($validated['products'] as $product) {
            $purchase->items()->create($product);
        }

        // Inventory Transaction Effect


        // Accounts Transaction Effect
            $purchase->amount=$purchase->net_payable;
            accountsTransaction('GPB', $purchase, 13, 12);

        // Supplier Transaction Effect
        $supplierTransaction = [
            'supplier_id' => $purchase->supplier_id,
            'doc_type' => 'GPB',
            'doc_id' => $purchase->id,
            'amount' => $purchase->net_payable,
            'date' => $purchase->date,
            'transaction_type' => 1,
            'chart_of_account_id' => 12,
            'description' => 'Purchase of goods',
        ];
        SupplierTransaction::create($supplierTransaction);


//            DB::commit();
//        } catch (\Exception $exception) {
//            DB::rollBack();
//            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
//            return back();
//        }
        Toastr::success('Purchase Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('purchases.create');

    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        $data = [
            'model' => $purchase,

        ];

        return view('purchase.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($purchase)
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'supplier_groups' => SupplierGroup::all(),
            'suppliers' => Supplier::all(),
            'stores' => Store::where(['type' => 'RM'])->get(),
            'purchase' => Purchase::with('supplier')->find(decrypt($purchase))
        ];
        return view('purchase.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
//        dd( $request->all());
        $purchase->subtotal = $request->subtotal;
        $purchase->net_payable = $request->net_payable;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->vat = $request->vat;
        $purchase->remark = $request->remark;
        $purchase->updated_by = Auth::guard('web')->id();
        $purchase->save();
        PurchaseItem::where('purchase_id', $purchase->id)->delete();
        $products = $request->get('products');
        foreach ($products as $row) {
            $purchase->items()->create($row);
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

        return $pdf->download($name . '.pdf');
    }
}
