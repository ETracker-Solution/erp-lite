<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRMInventoryTransferRequest;
use App\Http\Requests\UpdateFGInventoryTransferRequest;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransfer;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class RMInventoryTransferController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = InventoryTransfer::query()
                ->select([
                    'inventory_transfers.id',
                    'inventory_transfers.uid',
                    'inventory_transfers.date',
                    'inventory_transfers.status',
                    'inventory_transfers.from_store_id',
                    'inventory_transfers.to_store_id',
                    'inventory_transfers.created_at',
                ])
                ->with(['fromStore:id,name', 'toStore:id,name'])
                ->where('inventory_transfers.type', 'RM')
                ->latest('inventory_transfers.id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->addColumn('action', function ($row) {
                    return view('rm_inventory_transfer.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'status', 'created_at'])
                ->make(true);
        }

        return view('rm_inventory_transfer.index');
    }

    public function create()
    {
        $factoryId = auth()->user()?->employee?->factory_id;

        $stores = Store::query()
            ->whereType('RM')
            ->when($factoryId, fn ($q) => $q->where(['doc_type' => 'factory', 'doc_id' => $factoryId]))
            ->orderBy('name')
            ->get(['id', 'name']);

        $data = [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'RM'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'stores' => $stores,
            'to_stores' => Store::query()->whereType('RM')->orderBy('name')->get(['id', 'name']),
            'serial_no' => (int) InventoryTransfer::query()->max('id') + 1,
        ];

        return view('rm_inventory_transfer.create', $data);
    }

    public function store(StoreRMInventoryTransferRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $data['uid'] = generateUniqueUUID($data['from_store_id'], InventoryTransfer::class, 'uid');
            $transfer = InventoryTransfer::create($data);
            foreach ($data['products'] as $product) {
                $transfer->items()->create($product);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('RM Inventory Transfer Successful!.', '', ["progressBar" => true]);
        return redirect()->route('rm-inventory-transfers.index');
    }

    public function show($id)
    {
        $RMInventoryTransfer = InventoryTransfer::query()
            ->with([
                'fromStore:id,name',
                'toStore:id,name',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->where('type', 'RM')
            ->findOrFail($id);

        $items = $RMInventoryTransfer->items;

        return view('rm_inventory_transfer.show', compact('RMInventoryTransfer', 'items'));
    }

    public function edit(InventoryTransfer $inventoryTransfer)
    {
        //
    }

    public function update(UpdateFGInventoryTransferRequest $request, InventoryTransfer $inventoryTransfer)
    {
        //
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transfer = InventoryTransfer::query()->where('type', 'RM')->findOrFail($id);
            if ($transfer->status === 'received') {
                Toastr::info('Received transfer cannot be deleted!.', '', ["progressBar" => true]);
                return back();
            }
            $transfer->items()->delete();
            $transfer->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('Inventory Transfer Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('rm-inventory-transfers.index');
    }

    public function pdfDownload($id)
    {
        $RMInventoryTransfer = InventoryTransfer::query()
            ->with([
                'fromStore:id,name',
                'toStore:id,name',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->where('type', 'RM')
            ->findOrFail($id);

        $pdf = PDF::loadView(
            'rm_inventory_transfer.pdf',
            [
                'RMInventoryTransfer' => $RMInventoryTransfer,
                'items' => $RMInventoryTransfer->items,
            ],
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin_left' => 1,
                'margin_right' => 1,
                'margin_top' => 1,
                'margin_bottom' => 1,
            ]
        );

        return $pdf->stream('RM-Transfer-' . ($RMInventoryTransfer->uid ?: $RMInventoryTransfer->id) . '.pdf');
    }
}
