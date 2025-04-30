<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRequest;
use App\Http\Requests\UpdateProductionRequest;
use App\Models\AccountTransaction;
use App\Models\Batch;
use App\Models\ChartOfInventory;
use App\Models\Factory;
use App\Models\InventoryTransaction;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Production;

use App\Models\ProductionItem;
use App\Models\ProductionRecipe;
use App\Models\Purchase;
use App\Models\Requisition;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ExportService;
use Carbon\Carbon;
use stdClass;

class ProductionController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }
    public function exportFGProduction($type)
    {
        $data = ProductionItem::with('coi.parent', 'production.batch')->latest();

        if (\request()->filled('date_range')) {
            [$from_date, $to_date] = explode(' to ', request()->date_range);

            $from_date = Carbon::parse($from_date)->format('Y-m-d');
            $to_date = Carbon::parse($to_date)->format('Y-m-d');

            $data = $data->whereHas('production', function ($query) use ($from_date, $to_date) {
                $query->whereDate('date', '>=', $from_date)
                    ->whereDate('date', '<=', $to_date);
            });
        }

        $exportableData = [
            'productions' => $data->get()
        ];
        $viewFileName = 'fg_production';
        $filenameToDownload = date('ymdHis') . '_fg_production';
        return $this->exportService->exportFile($type, $viewFileName, $exportableData, $filenameToDownload, 'L'); // L stands for Landscape, if Portrait needed, just remove this params

    }
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
            $productions = $this->getFilteredData();
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

    protected function getFilteredData()
    {
        $data =  Production::with('batch', 'factory', 'store');
        if (request()->filled('date_range')){
            searchColumnByDateRange($data);
        }

        return $data->latest();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $requisitions = Requisition::select('id','uid')->get();
        $serial_count = Production::latest()->first() ? Production::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'batches' => Batch::where(['is_production' => false])->get(),
            'factories' => Factory::query()->get(),
            'stores' => Store::where(['type' => 'FG', 'doc_type' => 'factory'])->get(),
            'serial_no' => $serial_no,
            'requisitions' => $requisitions,

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
            $factory = Factory::find($validated['factory_id']);
            $rm_store = Store::where(['doc_type' => 'factory', 'doc_id' => $factory->id, 'type' => 'RM'])->first();
            $validated['uid'] = generateUniqueUUID($store->doc_id, Production::class, 'uid',$store->doc_type == 'factory');
            $production = Production::query()->create($validated);
            Batch::where('id', $validated['batch_id'])->update(['is_production' => true]);
            $totalRate = 0;
            foreach ($validated['products'] as $product) {
                if ($product['quantity'] > 0){
                    $totalRate += $product['quantity'] * $product['rate'];
                    $production->items()->create($product);

                    $items['store_id'] = $production->store_id;
                    $items['doc_type'] = 'FGP';
                    $items['id'] = $production->id;
                    $items['quantity'] = $product['quantity'];
                    $items['rate'] = averageFGRate($product['coi_id']);
                    $items['amount'] = $product['quantity'] * $items['rate'];
                    $items['date'] = $production->date;
                    $items['type'] = 1;
                    $items['coi_id'] = $product['coi_id'];

                    if (!$rm_store) {
                        Toastr::error('Please Set RM Store', '', ["progressBar" => true]);
                        return back();
                    }

                    $recipes_items = ProductionRecipe::where('fg_id', $items['coi_id'])->get();
                    $currentRMStock = 0;
                    foreach ($recipes_items as $recipe_item) {
                        $currentRMStock = availableInventoryBalance($recipe_item->rm_id, $rm_store->id);
                        $rm_qty = $recipe_item->qty * $product['quantity'];
                        if ($currentRMStock < $rm_qty) {
                            Toastr::error('Stock Not Available' . ' !', '', ["progressBar" => true]);
                            return back();
                        }
                        $rm = new stdClass();
                        $rm->date = date('Y-m-d');
                        $rm->coi_id = $recipe_item->rm_id;
                        $rm->rate = 0;
                        $rm->amount = 0;
                        $rm->store_id = $rm_store->id;
                        $rm->quantity = $rm_qty;
                        $rm->id = $recipe_item->id;
                        addInventoryTransaction(-1, 'FGP', (object)$rm);
                    }
                    addInventoryTransaction(1, 'FGP', (object)$items);
                }
            }
            $production->update([
                'subtotal'=>$totalRate
            ]);

            $production->amount = $totalRate;
            // Accounts Transaction Effect
            addAccountsTransaction('FGP', $production, 16, 17);

            DB::commit();
        } catch (\Exception $exception) {
            dd($exception);
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
        return view('production.show', compact(var_name: 'production'));
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
