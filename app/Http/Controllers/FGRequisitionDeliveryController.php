<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGRequisitionDeliveryRequest;
use App\Models\InventoryTransaction;
use App\Models\Requisition;
use App\Models\RequisitionDelivery;
use App\Models\RequisitionItem;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Yajra\DataTables\Facades\DataTables;

class FGRequisitionDeliveryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = RequisitionDelivery::query()
                ->select([
                    'requisition_deliveries.id',
                    'requisition_deliveries.uid',
                    'requisition_deliveries.date',
                    'requisition_deliveries.status',
                    'requisition_deliveries.total_quantity',
                    'requisition_deliveries.from_store_id',
                    'requisition_deliveries.to_store_id',
                    'requisition_deliveries.requisition_id',
                    'requisition_deliveries.created_at',
                ])
                ->where('requisition_deliveries.type', 'FG')
                ->with([
                    'fromStore:id,name',
                    'toStore:id,name',
                    'requisition:id,uid',
                ])
                ->latest('requisition_deliveries.id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('total_quantity', fn ($row) => number_format((float) $row->total_quantity, 2))
                ->addColumn('action', function ($row) {
                    return view('fg_requisition_delivery.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }

        return view('fg_requisition_delivery.index');
    }

    public function create()
    {
        $factoryId = auth()->user()?->employee?->factory_id;
        $factoryStoreIds = $factoryId
            ? Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'factory', 'doc_id' => $factoryId])
                ->pluck('id')
            : collect();

        return view('fg_requisition_delivery.create', [
            'from_stores' => Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'factory', 'status' => 'active'])
                ->when($factoryId, fn ($q) => $q->where('doc_id', $factoryId))
                ->orderBy('name')
                ->get(['id', 'name']),
            'to_stores' => Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'outlet', 'status' => 'active'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'requisitions' => Requisition::query()
                ->select(['id', 'uid', 'date', 'from_store_id', 'to_store_id', 'status', 'delivery_status', 'type'])
                ->where(['type' => 'FG', 'status' => 'approved'])
                ->whereIn('delivery_status', ['pending', 'partial'])
                ->when($factoryStoreIds->isNotEmpty(), fn ($q) => $q->whereIn('to_store_id', $factoryStoreIds))
                ->latest('id')
                ->get(),
        ]);
    }

    public function store(StoreFGRequisitionDeliveryRequest $request)
    {
        $data = $request->validated();
        $products = collect($request->get('products', []))
            ->filter(fn ($p) => (float) ($p['quantity'] ?? 0) > 0)
            ->values()
            ->all();

        if (empty($products)) {
            Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
            return back();
        }

        try {
            DB::beginTransaction();

            $fromStore = Store::findOrFail($data['from_store_id']);
            $data['uid'] = generateUniqueUUID(
                $fromStore->doc_id,
                RequisitionDelivery::class,
                'uid',
                $fromStore->doc_type === 'factory',
                $fromStore->doc_type === 'ho'
            );

            $requisitionDelivery = RequisitionDelivery::query()->create($data);

            foreach ($products as $product) {
                $product['requisition_id'] = $data['requisition_id'];
                $requisitionDelivery->items()->create($product);
            }

            Requisition::where('id', $data['requisition_id'])->update([
                'delivery_status' => $request->input('delivery_status', 'full'),
            ]);

            DB::commit();
            Toastr::success('FG Requisition Delivery Entry Successful!.', '', ["progressBar" => true]);
            return redirect()->route('fg-requisition-deliveries.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
    }

    public function show(string $id)
    {
        $fgRequisitionDelivery = RequisitionDelivery::query()
            ->with([
                'toStore:id,name',
                'fromStore:id,name',
                'requisition:id,uid,outlet_id',
                'requisition.outlet:id,name,address',
                'createdBy:id,name',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        $requisitionItemQtys = $this->requisitionItemQuantities($fgRequisitionDelivery);

        return view('fg_requisition_delivery.show', compact('fgRequisitionDelivery', 'requisitionItemQtys'));
    }

    public function edit(string $id)
    {
        $fgRequisitionDelivery = RequisitionDelivery::query()
            ->with([
                'toStore:id,name',
                'fromStore:id,name',
                'requisition:id,uid,outlet_id',
                'requisition.outlet:id,name,address',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        $requisitionItemQtys = $this->requisitionItemQuantities($fgRequisitionDelivery);

        return view('fg_requisition_delivery.edit', compact('fgRequisitionDelivery', 'requisitionItemQtys'));
    }

    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $fgRequisitionDelivery = RequisitionDelivery::findOrFail(decrypt($id));
            $items = $request->items ?? [];
            $totalQty = 0;

            foreach ($items as $key => $item) {
                $prev = $fgRequisitionDelivery->items()->where('id', $key)->first();
                if (!$prev) {
                    continue;
                }

                $currentStock = InventoryTransaction::where('coi_id', $prev->coi->id)
                    ->where('store_id', $fgRequisitionDelivery->from_store_id)
                    ->select('coi_id', DB::raw('SUM(quantity * type) AS total_sum'))
                    ->groupBy('coi_id')
                    ->pluck('total_sum')
                    ->toArray();

                $deliveredQty = $prev->coi->requisitionDeliveryItems()
                    ->where('requisition_delivery_id', '!=', $fgRequisitionDelivery->id)
                    ->whereHas('requisitionDelivery', function ($q) {
                        return $q->where('status', 'completed');
                    })->sum('quantity');

                $preOrderDeliveredQty = $prev->coi->preOrderItems()
                    ->whereHas('preOrder', function ($q) {
                        return $q->where('status', 'delivered');
                    })->sum('quantity');

                $stock = ($currentStock[0] ?? 0) - $deliveredQty - $preOrderDeliveredQty;
                if (max($stock, 0) < $item) {
                    Toastr::warning('No Available Stock for ' . $prev->coi->name, '', ["progressBar" => true]);
                    return back();
                }

                $price = $prev->quantity != 0 ? ($prev->rate / $prev->quantity) : 0;
                $prev->update([
                    'quantity' => $item,
                    'rate' => $price * $item,
                ]);
                $totalQty += $item;
            }

            $fgRequisitionDelivery->update(['total_quantity' => $totalQty]);
            DB::commit();
            Toastr::success('FG Requisition Delivery Updated Successful!.', '', ["progressBar" => true]);
            return redirect()->route('fg-requisition-deliveries.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            RequisitionDelivery::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('FG Requisition Delivery Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('fg-requisition-deliveries.index');
    }

    public function pdfDownload($id)
    {
        $fgRequisitionDelivery = RequisitionDelivery::query()
            ->with([
                'toStore:id,name',
                'fromStore:id,name',
                'requisition:id,uid,outlet_id',
                'requisition.outlet:id,name,address',
                'createdBy:id,name',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        $requisitionItemQtys = $this->requisitionItemQuantities($fgRequisitionDelivery);

        $pdf = Pdf::loadView(
            'fg_requisition_delivery.pdf',
            [
                'fgRequisitionDelivery' => $fgRequisitionDelivery,
                'requisitionItemQtys' => $requisitionItemQtys,
            ],
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

        return $pdf->stream('FGRD-' . ($fgRequisitionDelivery->uid ?: $fgRequisitionDelivery->id) . '.pdf');
    }

    private function requisitionItemQuantities(RequisitionDelivery $delivery)
    {
        return RequisitionItem::query()
            ->where('requisition_id', $delivery->requisition_id)
            ->pluck('quantity', 'coi_id');
    }
}
