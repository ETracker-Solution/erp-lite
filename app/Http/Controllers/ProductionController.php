<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRequest;
use App\Http\Requests\UpdateProductionRequest;
use App\Models\AccountTransaction;
use App\Models\Batch;
use App\Models\ChartOfInventory;
use App\Models\Factory;
use App\Models\InventoryTransaction;
use App\Models\Production;
use App\Models\ProductionItem;
use App\Models\Store;
use App\Services\ExportService;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Yajra\DataTables\Facades\DataTables;

class ProductionController extends Controller
{
    protected $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    public function exportFGProduction($type)
    {
        $data = ProductionItem::query()
            ->with(['coi:id,name,parent_id', 'coi.parent:id,name', 'production:id,date,batch_id', 'production.batch:id,batch_no'])
            ->latest('id');

        if (request()->filled('date_range')) {
            [$from_date, $to_date] = getDatesArrayFromDateRange(request()->date_range);
            $from_date = Carbon::parse($from_date)->format('Y-m-d');
            $to_date = Carbon::parse($to_date)->format('Y-m-d');

            $data->whereHas('production', function ($query) use ($from_date, $to_date) {
                $query->whereDate('date', '>=', $from_date)
                    ->whereDate('date', '<=', $to_date);
            });
        }

        return $this->exportService->exportFile(
            $type,
            'fg_production',
            ['productions' => $data->get()],
            date('ymdHis') . '_fg_production',
            'L'
        );
    }

    public function index()
    {
        if (request()->ajax()) {
            $query = Production::query()
                ->select([
                    'productions.id',
                    'productions.uid',
                    'productions.date',
                    'productions.status',
                    'productions.subtotal',
                    'productions.total_quantity',
                    'productions.store_id',
                    'productions.factory_id',
                    'productions.batch_id',
                    'productions.created_at',
                ])
                ->with([
                    'store:id,name',
                    'factory:id,name',
                    'batch:id,batch_no',
                ])
                ->latest('id');

            if (request()->filled('date_range')) {
                searchColumnByDateRange($query, 'date');
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('subtotal', fn ($row) => number_format((float) $row->subtotal, 2))
                ->editColumn('total_quantity', fn ($row) => number_format((float) $row->total_quantity, 2))
                ->addColumn('action', function ($row) {
                    return view('production.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['status', 'action', 'created_at'])
                ->make(true);
        }

        return view('production.index');
    }

    public function create()
    {
        $factoryId = auth()->user()?->employee?->factory_id;

        return view('production.create', [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'FG'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'batches' => Batch::query()
                ->where('is_production', false)
                ->latest('id')
                ->get(['id', 'batch_no']),
            'factories' => Factory::query()
                ->when($factoryId, fn ($q) => $q->where('id', $factoryId))
                ->orderBy('name')
                ->get(['id', 'name']),
            'stores' => Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'factory'])
                ->when($factoryId, fn ($q) => $q->where('doc_id', $factoryId))
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(StoreProductionRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated['products'])) {
            Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
            return back();
        }

        DB::beginTransaction();
        try {
            $store = Store::findOrFail($validated['store_id']);
            $validated['uid'] = generateUniqueUUID(
                $store->doc_id,
                Production::class,
                'uid',
                $store->doc_type === 'factory',
                $store->doc_type === 'ho'
            );

            $production = Production::query()->create($validated);
            Batch::where('id', $validated['batch_id'])->update(['is_production' => true]);

            $totalAmount = 0;
            $totalQty = 0;
            foreach ($validated['products'] as $product) {
                $qty = (float) ($product['quantity'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $lineAmount = $qty * (float) $product['rate'];
                $totalAmount += $lineAmount;
                $totalQty += $qty;
                $production->items()->create($product);

                InventoryTransaction::query()->create([
                    'store_id' => $production->store_id,
                    'doc_type' => 'FGP',
                    'doc_id' => $production->id,
                    'quantity' => $qty,
                    'rate' => $product['rate'],
                    'amount' => $lineAmount,
                    'date' => $production->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            $production->subtotal = $totalAmount;
            $production->total_quantity = $totalQty;
            $production->save();

            // GL helper expects $doc->amount (in-memory; column is subtotal).
            $production->amount = $totalAmount;
            addAccountsTransaction('FGP', $production, getFGInventoryGLId(), getWIPGLId());

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('Production Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('productions.index');
    }

    public function show($id)
    {
        $production = Production::query()
            ->with([
                'store:id,name',
                'factory:id,name',
                'batch:id,batch_no',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        return view('production.show', compact('production'));
    }

    public function edit($id)
    {
        $factoryId = auth()->user()?->employee?->factory_id;

        return view('production.edit', [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'FG'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'batches' => Batch::query()->latest('id')->get(['id', 'batch_no', 'is_production']),
            'factories' => Factory::query()
                ->when($factoryId, fn ($q) => $q->where('id', $factoryId))
                ->orderBy('name')
                ->get(['id', 'name']),
            'stores' => Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'factory'])
                ->when($factoryId, fn ($q) => $q->where('doc_id', $factoryId))
                ->orderBy('name')
                ->get(['id', 'name']),
            'production' => Production::with('items')->findOrFail(decrypt($id)),
        ]);
    }

    public function update(UpdateProductionRequest $request, Production $production)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            Batch::where('id', $production->batch_id)->update(['is_production' => false]);
            $production->update($validated);
            Batch::where('id', $validated['batch_id'])->update(['is_production' => true]);
            ProductionItem::where('production_id', $production->id)->delete();
            InventoryTransaction::where(['doc_id' => $production->id, 'doc_type' => 'FGP'])->delete();

            $totalAmount = 0;
            $totalQty = 0;
            foreach ($validated['products'] as $product) {
                $qty = (float) ($product['quantity'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $lineAmount = $qty * (float) $product['rate'];
                $totalAmount += $lineAmount;
                $totalQty += $qty;
                $production->items()->create($product);

                InventoryTransaction::query()->create([
                    'store_id' => $production->store_id,
                    'doc_type' => 'FGP',
                    'doc_id' => $production->id,
                    'quantity' => $qty,
                    'rate' => $product['rate'],
                    'amount' => $lineAmount,
                    'date' => $production->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            $production->subtotal = $totalAmount;
            $production->total_quantity = $totalQty;
            $production->save();
            $production->amount = $totalAmount;

            AccountTransaction::where(['doc_id' => $production->id, 'doc_type' => 'FGP'])->delete();
            addAccountsTransaction('FGP', $production, getFGInventoryGLId(), getWIPGLId());
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('Production Updated Successful!.', '', ["progressBar" => true]);
        return redirect()->route('productions.index');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $production = Production::findOrFail(decrypt($id));
            Batch::where('id', $production->batch_id)->update(['is_production' => false]);
            AccountTransaction::where(['doc_id' => $production->id, 'doc_type' => 'FGP'])->delete();
            InventoryTransaction::where(['doc_id' => $production->id, 'doc_type' => 'FGP'])->delete();
            $production->items()->delete();
            $production->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('Production Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('productions.index');
    }

    public function productionPdf($id)
    {
        $production = Production::query()
            ->with([
                'store:id,name',
                'factory:id,name',
                'batch:id,batch_no',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        $pdf = Pdf::loadView(
            'production.pdf',
            ['production' => $production],
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin_left' => 8,
                'margin_right' => 8,
                'margin_top' => 8,
                'margin_bottom' => 8,
            ]
        );

        return $pdf->stream('FGP-' . ($production->uid ?: $production->id) . '.pdf');
    }
}
