<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRMTransferReceiveRequest;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransfer;
use App\Models\Store;
use App\Models\TransferReceive;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class RMTransferReceiveController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = TransferReceive::query()
                ->select([
                    'transfer_receives.id',
                    'transfer_receives.uid',
                    'transfer_receives.date',
                    'transfer_receives.status',
                    'transfer_receives.from_store_id',
                    'transfer_receives.to_store_id',
                    'transfer_receives.inventory_transfer_id',
                    'transfer_receives.created_at',
                ])
                ->with([
                    'fromStore:id,name',
                    'toStore:id,name',
                    'inventoryTransfer:id,uid',
                ])
                ->where('type', 'RM')
                ->latest('id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->addColumn('action', function ($row) {
                    return view('rm_inventory_transfer_receive.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }

        return view('rm_inventory_transfer_receive.index');
    }

    public function create()
    {
        $factoryId = auth()->user()?->employee?->factory_id;

        $inventory_transfers = InventoryTransfer::query()
            ->select('id', 'uid', 'date', 'from_store_id', 'to_store_id', 'status', 'type')
            ->with(['fromStore:id,name', 'toStore:id,name'])
            ->where(['type' => 'RM', 'status' => 'pending'])
            ->when($factoryId, function ($q) use ($factoryId) {
                $q->whereHas('toStore', function ($query) use ($factoryId) {
                    $query->where(['doc_type' => 'factory', 'doc_id' => $factoryId]);
                });
            })
            ->latest('id')
            ->get();

        $stores = Store::query()->where(['type' => 'RM'])->orderBy('name')->get(['id', 'name']);

        return view('rm_inventory_transfer_receive.create', [
            'from_stores' => $stores,
            'to_stores' => $stores,
            'inventory_transfers' => $inventory_transfers,
        ]);
    }

    public function store(StoreRMTransferReceiveRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();

            $transfer = InventoryTransfer::query()
                ->where(['id' => $data['inventory_transfer_id'], 'type' => 'RM'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($transfer->status === 'received') {
                DB::rollBack();
                Toastr::info('This transfer already received!.', '', ["progressBar" => true]);
                return back();
            }

            $store = Store::findOrFail($data['to_store_id']);
            $data['uid'] = generateUniqueUUID($store->id, TransferReceive::class, 'uid');
            $receive = TransferReceive::query()->create($data);

            foreach ($request->get('products', []) as $product) {
                $receive->items()->create($product);

                InventoryTransaction::query()->create([
                    'store_id' => $receive->from_store_id,
                    'doc_type' => 'RMIT',
                    'doc_id' => $receive->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $receive->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
                InventoryTransaction::query()->create([
                    'store_id' => $receive->to_store_id,
                    'doc_type' => 'RMIT',
                    'doc_id' => $receive->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $receive->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            $transfer->update(['status' => 'received']);
            DB::commit();
            Toastr::success('RM Transfer Receive Entry Successful!.', '', ["progressBar" => true]);
            return redirect()->route('rm-transfer-receives.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
    }

    public function show(string $id)
    {
        $rmTransferReceive = TransferReceive::query()
            ->with([
                'fromStore:id,name',
                'toStore:id,name',
                'inventoryTransfer:id,uid',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->where('type', 'RM')
            ->findOrFail(decrypt($id));

        return view('rm_inventory_transfer_receive.show', compact('rmTransferReceive'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        // Stock already posted — delete not exposed.
    }

    public function pdf($id)
    {
        $rmTransferReceive = TransferReceive::query()
            ->with([
                'fromStore:id,name',
                'toStore:id,name',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->where('type', 'RM')
            ->findOrFail(decrypt($id));

        $pdf = PDF::loadView(
            'rm_inventory_transfer_receive.pdf',
            compact('rmTransferReceive'),
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

        return $pdf->stream('RM-Transfer-Receive-' . ($rmTransferReceive->uid ?: $rmTransferReceive->id) . '.pdf');
    }
}
