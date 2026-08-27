<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGTransferReceiveRequest;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransfer;
use App\Models\Store;
use App\Models\TransferReceive;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class FGTransferReceiveController extends Controller
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
                ->where('transfer_receives.type', 'FG')
                ->latest('transfer_receives.id');

            if (!auth()->user()->is_super && auth()->user()->employee) {
                $storeIds = collect();
                if (auth()->user()->employee->factory_id) {
                    $storeIds = auth()->user()->employee->factory->stores()->pluck('id');
                } elseif (auth()->user()->employee->outlet_id) {
                    $storeIds = auth()->user()->employee->outlet->stores()->pluck('id');
                }
                if ($storeIds->isNotEmpty()) {
                    $query->where(function ($q) use ($storeIds) {
                        $q->whereIn('from_store_id', $storeIds)
                            ->orWhereIn('to_store_id', $storeIds);
                    });
                }
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->addColumn('action', function ($row) {
                    return view('fg_inventory_transfer_receive.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }

        return view('fg_inventory_transfer_receive.index');
    }

    public function create()
    {
        $outletId = auth()->user()?->employee?->outlet_id;
        $factoryId = auth()->user()?->employee?->factory_id;
        $prefillTransferId = request()->integer('transfer_id') ?: null;

        $transfersQuery = InventoryTransfer::query()
            ->select(['id', 'uid', 'date', 'from_store_id', 'to_store_id', 'status', 'type'])
            ->where(['type' => 'FG', 'status' => 'pending'])
            ->latest('id');

        if ($outletId) {
            $transfersQuery->whereHas('toStore', fn ($q) => $q->where(['doc_type' => 'outlet', 'doc_id' => $outletId]));
        } elseif ($factoryId) {
            $transfersQuery->whereHas('toStore', fn ($q) => $q->where(['doc_type' => 'factory', 'doc_id' => $factoryId]));
        }

        $stores = Store::query()->where(['type' => 'FG'])->orderBy('name')->get(['id', 'name']);

        return view('fg_inventory_transfer_receive.create', [
            'from_stores' => $stores,
            'to_stores' => $stores,
            'inventory_transfers' => $transfersQuery->get(),
            'prefillTransferId' => $prefillTransferId,
        ]);
    }

    public function store(StoreFGTransferReceiveRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            $transfer = InventoryTransfer::query()
                ->where(['id' => $data['inventory_transfer_id'], 'type' => 'FG'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($transfer->status === 'received') {
                DB::rollBack();
                Toastr::info('This transfer already received!.', '', ['progressBar' => true]);

                return back();
            }

            $store = Store::findOrFail($data['to_store_id']);
            $data['uid'] = generateUniqueUUID($store->doc_id, TransferReceive::class, 'uid');
            $fGInventoryTransfer = TransferReceive::query()->create($data);
            $products = $request->get('products', []);

            foreach ($products as $product) {
                $fGInventoryTransfer->items()->create($product);
                InventoryTransaction::query()->create([
                    'store_id' => $fGInventoryTransfer->from_store_id,
                    'doc_type' => 'FGIT',
                    'doc_id' => $fGInventoryTransfer->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $fGInventoryTransfer->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
                InventoryTransaction::query()->create([
                    'store_id' => $fGInventoryTransfer->to_store_id,
                    'doc_type' => 'FGIT',
                    'doc_id' => $fGInventoryTransfer->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $fGInventoryTransfer->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            InventoryTransfer::where('id', $data['inventory_transfer_id'])->update(['status' => 'received']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }

        Toastr::success('FG Transfer Receive Entry Successful!.', '', ['progressBar' => true]);

        return redirect()->route('fg-transfer-receives.index');
    }

    public function show($id)
    {
        $fgTransferReceive = TransferReceive::query()
            ->with([
                'toStore:id,name',
                'fromStore:id,name',
                'createdBy:id,name',
                'inventoryTransfer:id,uid',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        return view('fg_inventory_transfer_receive.show', compact('fgTransferReceive'));
    }

    public function pdf($id)
    {
        $fgTransferReceive = TransferReceive::query()
            ->with(['toStore:id,name', 'fromStore:id,name', 'createdBy:id,name', 'items.coi.parent', 'items.coi.unit'])
            ->findOrFail(decrypt($id));

        $pdf = PDF::loadView(
            'fg_inventory_transfer_receive.pdf',
            ['fgTransferReceive' => $fgTransferReceive],
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin-left' => 1,
                '', '', 0, '', 1, 1, 1, 1, 1, 1, 'L',
            ]
        );

        return $pdf->stream(\Carbon\Carbon::now()->format('d-m-Y') . '.pdf');
    }
}
