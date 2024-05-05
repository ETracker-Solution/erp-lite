<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Supplier;
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
        $data = [
            'categories' => DB::table('categories')->get(),
            'suppliers' => Supplier::all(),

        ];

        return view('purchase.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        $request->validate([
            'products' => 'array',
            'description' => 'nullable',
        ]);

        //dd( $request->all());
//        try {
//            DB::beginTransaction();
            $purchase = new Purchase();
            $purchase->purchase_number = \App\Classes\PurchaseNumber::serial_number();
            $purchase->subtotal = $request->subtotal;
            $purchase->grand_total = $request->subtotal;
            $purchase->date = Carbon::now()->format('Y-m-d');
            $purchase->description = $request->description;
            $purchase->supplier_id = $request->supplier_id;
            $purchase->created_by = Auth::id();
            $purchase->save();

            $products = $request->get('products');
            foreach ($products as $row) {
                //Set as batch number
                $row['batch_number'] = \App\Classes\BatchNumber::serial_number();

                $purchase->items()->create($row);
                $purchase->stock_items()->create($row);
//            Product::where('id', $row['product_id'])->update(['selling_price' => $row['selling_price'], 'buying_price' => $row['buying_price']]);
            }
            //DB::commit();
            Toastr::success('Messages in here', 'Title', ["positionClass" => "toast-top-center"]);
            return back();
//        } catch (\Exception $e) {
//            DB::rollBack();
//            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
//            Toastr::info('Something went wrong!.', '', ["progressbar" => true]);
//            return back();
//        }
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
    public function edit(Purchase $purchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        //
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
