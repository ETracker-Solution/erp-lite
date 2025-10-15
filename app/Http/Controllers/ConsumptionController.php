<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsumptionRequest;
use App\Http\Requests\UpdateConsumptionRequest;
use App\Models\AccountTransaction;
use App\Models\Batch;
use App\Models\ChartOfInventory;
use App\Models\Consumption;
use App\Models\ConsumptionItem;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Production;
use App\Models\PurchaseItem;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Yajra\DataTables\Facades\DataTables;

class ConsumptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\request()->ajax()) {
            $consumptions = Consumption::latest();
            return DataTables::of($consumptions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('consumption.action', compact('row'));
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
        return view('consumption.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $serial_count = Consumption::latest()->first() ? Consumption::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' => Store::where(['type' => 'RM', 'doc_type' => 'factory'])->get(),
            'batches' => Batch::where(['is_consumption' => false])->get(),
            'serial_no' => $serial_no,
            'store_url' => 1,
        ];

        return view('consumption.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreConsumptionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConsumptionRequest $request)
    {
        $validated = $request->validated();
//        DB::beginTransaction();
//        try {
        if (count($validated['products']) < 1) {
            Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
            return back();
        }
        $validated['serial_no'] = generateUniqueUUID($validated['store_id'], Consumption::class, 'serial_no', true);
        $consumption = Consumption::query()->create($validated);
        Batch::where('id', $validated['batch_id'])->update(['is_consumption' => true]);
        InventoryTransaction::where(['doc_id' => $consumption->id, 'doc_type' => 'RMC'])->delete();
        $consumption->amount = $consumption->subtotal;
        foreach ($validated['products'] as $product) {
            $consumption->items()->create($product);
            // Inventory Transaction Effect
            InventoryTransaction::query()->create([
                'store_id' => $consumption->store_id,
                'doc_type' => 'RMC',
                'doc_id' => $consumption->id,
                'quantity' => $product['quantity'],
                'rate' => $product['rate'],
                'amount' => $product['quantity'] * $product['rate'],
                'date' => $consumption->date,
                'type' => -1,
                'coi_id' => $product['coi_id'],
            ]);
        }
        // Accounts Transaction Effect
        AccountTransaction::where(['doc_id' => $consumption->id, 'doc_type' => 'RMC'])->delete();
        addAccountsTransaction('RMC', $consumption, 17, 15);


//            DB::commit();
//        } catch (\Exception $exception) {
//            DB::rollBack();
//            return $exception;
//            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
//            return back();
//        }
        Toastr::success('Consumption Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('consumptions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Consumption $consumption
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show($id)
    {
        $consumption = Consumption::findOrFail(decrypt($id));
        return view('consumption.show', compact('consumption'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Consumption $consumption
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' => Store::where(['type' => 'RM', 'doc_type' => 'factory'])->get(),
            'batches' => Batch::all(),
            'serial_no' => decrypt($id),
            'store_url' => 1,
            'consumption' => Consumption::findOrFail(decrypt($id)),
        ];

        return view('consumption.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateConsumptionRequest $request
     * @param \App\Models\Consumption $consumption
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateConsumptionRequest $request, Consumption $consumption)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Batch::where('id', $consumption->batch_id)->update(['is_consumption' => false]);
            $consumption->update($validated);
            Batch::where('id', $validated['batch_id'])->update(['is_consumption' => true]);
            ConsumptionItem::where('consumption_id', $consumption->id)->delete();
            InventoryTransaction::where(['doc_id' => $consumption->id, 'doc_type' => 'RMC'])->delete();
            $consumption->amount = $consumption->subtotal;
            foreach ($validated['products'] as $product) {
                $consumption->items()->create($product);
                // Inventory Transaction Effect
                InventoryTransaction::query()->create([
                    'store_id' => $consumption->store_id,
                    'doc_type' => 'RMC',
                    'doc_id' => $consumption->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $consumption->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
            }
            // Accounts Transaction Effect
            AccountTransaction::where(['doc_id' => $consumption->id, 'doc_type' => 'RMC'])->delete();
            addAccountsTransaction('RMC', $consumption, 17, 15);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Consumption Updated Successful!.', '', ["progressbar" => true]);
        return redirect()->route('consumptions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Consumption $consumption
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Consumption::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Consumption Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('consumptions.index');
    }

    public function consumptionPdf($id)
    {
        $stock_adjust = Consumption::findOrFail(decrypt($id));
        $data = [
            'stock_adjust' => $stock_adjust,
        ];

        $pdf = Pdf::loadView(
            'consumption.pdf',
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
