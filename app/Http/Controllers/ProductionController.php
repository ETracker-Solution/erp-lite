<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRequest;
use App\Http\Requests\UpdateProductionRequest;
use App\Models\AccountTransaction;
use App\Models\Batch;
use App\Models\ChartOfInventory;
use App\Models\Factory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Production;

use App\Models\ProductionItem;
use App\Models\Purchase;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Yajra\DataTables\Facades\DataTables;

class ProductionController extends Controller
{
    public function stock($production_id)
    {
        DB::beginTransaction();
        try {
            $production = Production::find(decrypt($production_id));
            $production->status = "received";
            $production->update();

            foreach ($production->items as $product) {
                $stock_item['production_id'] = $production->id;
                $stock_item['product_id'] = $product->product_id;
                $stock_item['unit_price'] = $product->unit_price;
                $stock_item['quantity'] = $product->quantity;
                $stock_item['type'] = 'finish';
                $stock_item['stocker_type'] = ProductionHouse::class;
                $stock_item['stocker_id'] = auth('factory')->user()->production_house_id;
                $production->stockItems()->create($stock_item);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Production Stocked Successfully!.', '', ["progressBar" => true]);
        return back();
    }

    public function index()
    {
        if (\request()->ajax()) {
            $productions = Production::with('batch', 'factory', 'store')->latest();
            return DataTables::of($productions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('production.action', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('production.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $serial_count = Production::latest()->first() ? Production::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'batches' => Batch::where(['is_production' => false])->get(),
            'factories' => Factory::query()->get(),
            'stores' => Store::where(['type' => 'FG', 'doc_type' => 'factory'])->get(),
            'serial_no' => $serial_no,

        ];
        return view('production.create', $data);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreProductionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductionRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (count($validated['products']) < 1) {
                Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
                return back();
            }
            $store = Store::find($validated['store_id']);
            $validated['uid'] = generateUniqueUUID($store->doc_id, Production::class, 'uid');
            $production = Production::query()->create($validated);
            Batch::where('id', $validated['batch_id'])->update(['is_production' => true]);
            $production->amount = $production->subtotal;
            foreach ($validated['products'] as $product) {
                $production->items()->create($product);
                // Inventory Transaction Effect
                InventoryTransaction::query()->create([
                    'store_id' => $production->store_id,
                    'doc_type' => 'FGP',
                    'doc_id' => $production->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $production->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }
            // Accounts Transaction Effect
            addAccountsTransaction('FGP', $production, 16, 17);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Production Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('productions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Production $production
     * @return \Illuminate\Http\Response
     */
    public function show(Production $production)
    {
        return view('production.show', compact('production'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Production $production
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {


        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'batches' => Batch::all(),
            'factories' => Factory::query()->get(),
            'stores' => Store::where(['type' => 'FG', 'doc_type' => 'factory'])->get(),
            'production' => Production::with('items')->find(decrypt($id))

        ];
        return view('production.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateProductionRequest $request
     * @param \App\Models\Production $production
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductionRequest $request, Production $production)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Batch::where('id', $production->batch_id)->update(['is_production' => false]);
            $production->update($validated);
            Batch::where('id', $validated['batch_id'])->update(['is_production' => true]);
            $production->amount = $production->subtotal;
            ProductionItem::where('production_id', $production->id)->delete();
            InventoryTransaction::where(['doc_id' => $production->id, 'doc_type' => 'FGP'])->delete();
            foreach ($validated['products'] as $product) {
                $production->items()->create($product);

                // Inventory Transaction Effect
                InventoryTransaction::query()->create([
                    'store_id' => $production->store_id,
                    'doc_type' => 'FGP',
                    'doc_id' => $production->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $production->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            // Accounts Transaction Effect
            AccountTransaction::where(['doc_id' => $production->id, 'doc_type' => 'FGP'])->delete();
            addAccountsTransaction('FGP', $production, 16, 17);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Production Updated Successful!.', '', ["progressbar" => true]);
        return redirect()->route('productions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Production $production
     * @return \Illuminate\Http\Response
     */
    public function destroy(Production $production)
    {
        //
    }

    public function productionPdf($id)
    {
        $production = Production::findOrFail(decrypt($id));
        $data = [
            'production' => $production,
        ];

        $pdf = Pdf::loadView(
            'production.pdf',
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
