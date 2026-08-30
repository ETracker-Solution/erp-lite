<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGInventoryTransferRequest;
use App\Http\Requests\UpdateFGInventoryTransferRequest;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransfer;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;

class FGInventoryTransferController extends Controller
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
                ->with([
                    'fromStore:id,name',
                    'toStore:id,name',
                ])
                ->where('inventory_transfers.type', 'FG')
                ->latest('inventory_transfers.id');

            if (!auth()->user()->is_super && auth()->user()->employee) {
                if (auth()->user()->employee->factory_id) {
                    $storeIds = auth()->user()->employee->factory->stores()->pluck('id');
                    $query->whereIn('from_store_id', $storeIds);
                } elseif (auth()->user()->employee->outlet_id) {
                    $storeIds = auth()->user()->employee->outlet->stores()->pluck('id');
                    $query->whereIn('from_store_id', $storeIds);
                }
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->addColumn('action', function ($row) {
                    return view('fg_inventory_transfer.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }

        return view('fg_inventory_transfer.index');
    }

    public function create()
    {
        $outletId = auth()->user()?->employee?->outlet_id;
        $factoryId = auth()->user()?->employee?->factory_id;

        if ($outletId) {
            $stores = Store::query()
                ->whereType('FG')
                ->where(['doc_type' => 'outlet', 'status' => 'active', 'doc_id' => $outletId])
                ->orderBy('name')
                ->get(['id', 'name']);
            $myStoreIds = $stores->pluck('id');
            $toStores = Store::query()
                ->whereType('FG')
                ->where('status', 'active')
                ->whereNotIn('id', $myStoreIds)
                ->orderBy('name')
                ->get(['id', 'name']);
        } elseif ($factoryId) {
            $stores = Store::query()
                ->whereType('FG')
                ->where(['doc_type' => 'factory', 'status' => 'active', 'doc_id' => $factoryId])
                ->orderBy('name')
                ->get(['id', 'name']);
            $toStores = Store::query()
                ->whereType('FG')
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name']);
        } else {
            $stores = Store::query()->whereType('FG')->where('status', 'active')->orderBy('name')->get(['id', 'name']);
            $toStores = $stores;
        }

        return view('fg_inventory_transfer.create', [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'FG'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'stores' => $stores,
            'to_stores' => $toStores,
        ]);
    }

    public function store(StoreFGInventoryTransferRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            $store = Store::findOrFail($data['from_store_id']);
            $headOffice = $store->doc_type === 'ho';
            $factory = $store->doc_type === 'factory';
            $data['uid'] = generateUniqueUUID($store->doc_id, InventoryTransfer::class, 'uid', $factory, $headOffice);
            $fGInventoryTransfer = InventoryTransfer::create($data);
            foreach ($data['products'] as $product) {
                $fGInventoryTransfer->items()->create($product);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('FG Inventory Transfer Successful!.', '', ['progressBar' => true]);

        return redirect()->route('fg-inventory-transfers.index');
    }

    public function show($id)
    {
        $fGInventoryTransfer = InventoryTransfer::query()
            ->with([
                'fromStore:id,name',
                'toStore:id,name',
                'createdBy:id,name',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        $items = $fGInventoryTransfer->items;

        return view('fg_inventory_transfer.show', compact('fGInventoryTransfer', 'items'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            InventoryTransfer::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('FG Inventory Transfer Deleted Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('fg-inventory-transfers.index');
    }

    public function pdfDownload($id)
    {
        $fGInventoryTransfer = InventoryTransfer::query()
            ->with(['fromStore:id,name', 'toStore:id,name', 'items.coi.parent', 'items.coi.unit'])
            ->findOrFail(decrypt($id));

        $pdf = PDF::loadView(
            'fg_inventory_transfer.pdf',
            [
                'items' => $fGInventoryTransfer->items,
                'FGInventoryTransfer' => $fGInventoryTransfer,
            ],
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
