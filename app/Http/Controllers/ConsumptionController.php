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
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Yajra\DataTables\Facades\DataTables;

class ConsumptionController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = Consumption::query()
                ->select([
                    'consumptions.id',
                    'consumptions.serial_no',
                    'consumptions.date',
                    'consumptions.status',
                    'consumptions.subtotal',
                    'consumptions.store_id',
                    'consumptions.batch_id',
                    'consumptions.created_at',
                ])
                ->with(['store:id,name', 'batch:id,batch_no'])
                ->latest('id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('subtotal', fn ($row) => number_format((float) $row->subtotal, 2))
                ->addColumn('action', function ($row) {
                    return view('consumption.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['status', 'action', 'created_at'])
                ->make(true);
        }

        return view('consumption.index');
    }

    public function create()
    {
        $factoryId = auth()->user()?->employee?->factory_id;

        return view('consumption.create', [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'RM'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'stores' => Store::query()
                ->where(['type' => 'RM', 'doc_type' => 'factory'])
                ->when($factoryId, fn ($q) => $q->where('doc_id', $factoryId))
                ->orderBy('name')
                ->get(['id', 'name']),
            'batches' => Batch::query()
                ->where('is_consumption', false)
                ->latest('id')
                ->get(['id', 'batch_no']),
        ]);
    }

    public function store(StoreConsumptionRequest $request)
    {
        $validated = $request->validated();

        if (empty($validated['products'])) {
            Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
            return back();
        }

        DB::beginTransaction();
        try {
            $store = Store::findOrFail($validated['store_id']);
            $validated['serial_no'] = generateUniqueUUID(
                $store->doc_id,
                Consumption::class,
                'serial_no',
                $store->doc_type === 'factory',
                $store->doc_type === 'ho'
            );

            $consumption = Consumption::query()->create($validated);
            Batch::where('id', $validated['batch_id'])->update(['is_consumption' => true]);

            $totalAmount = 0;
            foreach ($validated['products'] as $product) {
                $lineAmount = (float) $product['quantity'] * (float) $product['rate'];
                $totalAmount += $lineAmount;
                $consumption->items()->create($product);

                InventoryTransaction::query()->create([
                    'store_id' => $consumption->store_id,
                    'doc_type' => 'RMC',
                    'doc_id' => $consumption->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $lineAmount,
                    'date' => $consumption->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            $consumption->subtotal = $totalAmount;
            $consumption->save();

            // GL helper expects $doc->amount (in-memory; column is subtotal).
            $consumption->amount = $totalAmount;
            addAccountsTransaction('RMC', $consumption, getWIPGLId(), getRMInventoryGLId());

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('Consumption Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('consumptions.index');
    }

    public function show($id)
    {
        $consumption = Consumption::query()
            ->with([
                'store:id,name',
                'batch:id,batch_no',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        return view('consumption.show', compact('consumption'));
    }

    public function edit($id)
    {
        $data = [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'RM'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'stores' => Store::query()
                ->where(['type' => 'RM', 'doc_type' => 'factory'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'batches' => Batch::query()->latest('id')->get(['id', 'batch_no', 'is_consumption']),
            'store_url' => 1,
            'consumption' => Consumption::findOrFail(decrypt($id)),
        ];

        return view('consumption.edit', $data);
    }

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

            $totalAmount = 0;
            foreach ($validated['products'] as $product) {
                $lineAmount = (float) $product['quantity'] * (float) $product['rate'];
                $totalAmount += $lineAmount;
                $consumption->items()->create($product);

                InventoryTransaction::query()->create([
                    'store_id' => $consumption->store_id,
                    'doc_type' => 'RMC',
                    'doc_id' => $consumption->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $lineAmount,
                    'date' => $consumption->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            $consumption->subtotal = $totalAmount;
            $consumption->save();
            $consumption->amount = $totalAmount;

            AccountTransaction::where(['doc_id' => $consumption->id, 'doc_type' => 'RMC'])->delete();
            addAccountsTransaction('RMC', $consumption, getWIPGLId(), getRMInventoryGLId());
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('Consumption Updated Successful!.', '', ["progressBar" => true]);
        return redirect()->route('consumptions.index');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $consumption = Consumption::findOrFail(decrypt($id));
            Batch::where('id', $consumption->batch_id)->update(['is_consumption' => false]);
            AccountTransaction::where(['doc_id' => $consumption->id, 'doc_type' => 'RMC'])->delete();
            InventoryTransaction::where(['doc_id' => $consumption->id, 'doc_type' => 'RMC'])->delete();
            $consumption->items()->delete();
            $consumption->delete();
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
        $stock_adjust = Consumption::query()
            ->with([
                'store:id,name',
                'batch:id,batch_no',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        $pdf = Pdf::loadView(
            'consumption.pdf',
            ['stock_adjust' => $stock_adjust],
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

        return $pdf->stream('RMC-' . ($stock_adjust->serial_no ?: $stock_adjust->id) . '.pdf');
    }
}
