<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGDeliveryReceiveRequest;
use App\Models\DeliveryReceive;
use App\Models\InventoryTransaction;
use App\Models\RequisitionDelivery;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Yajra\DataTables\Facades\DataTables;

class FGDeliveryReceiveController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = DeliveryReceive::query()
                ->select([
                    'delivery_receives.id',
                    'delivery_receives.date',
                    'delivery_receives.status',
                    'delivery_receives.total_quantity',
                    'delivery_receives.from_store_id',
                    'delivery_receives.to_store_id',
                    'delivery_receives.requisition_delivery_id',
                    'delivery_receives.created_at',
                ])
                ->where('delivery_receives.type', 'FG')
                ->with([
                    'fromStore:id,name',
                    'toStore:id,name',
                    'requisitionDelivery:id,uid',
                ])
                ->latest('delivery_receives.id');

            $outletId = auth()->user()?->employee?->outlet_id;
            if ($outletId) {
                $storeIds = auth()->user()->employee->outlet->stores()->pluck('id');
                $query->whereIn('to_store_id', $storeIds);
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('total_quantity', fn ($row) => number_format((float) $row->total_quantity, 2))
                ->addColumn('action', function ($row) {
                    return view('fg_requisition_delivery_receive.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }

        return view('fg_requisition_delivery_receive.index');
    }

    public function create()
    {
        $outletId = auth()->user()?->employee?->outlet_id;

        $deliveriesQuery = RequisitionDelivery::query()
            ->select(['id', 'uid', 'date', 'from_store_id', 'to_store_id', 'status', 'type'])
            ->where(['type' => 'FG', 'status' => 'completed'])
            ->latest('id');

        if ($outletId) {
            $deliveriesQuery->whereIn('requisition_id', function ($query) use ($outletId) {
                $query->select('id')
                    ->from('requisitions')
                    ->where('outlet_id', $outletId);
            });
        }

        return view('fg_requisition_delivery_receive.create', [
            'from_stores' => Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'factory', 'status' => 'active'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'to_stores' => Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'outlet', 'status' => 'active'])
                ->when($outletId, fn ($q) => $q->where('doc_id', $outletId))
                ->orderBy('name')
                ->get(['id', 'name']),
            'requisition_deliveries' => $deliveriesQuery->get(),
        ]);
    }

    public function store(StoreFGDeliveryReceiveRequest $request)
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

            $reqDelivery = RequisitionDelivery::findOrFail($data['requisition_delivery_id']);
            if ($reqDelivery->status === 'received') {
                Toastr::error('Already Received');
                return back();
            }

            $deliveryReceive = DeliveryReceive::query()->create($data);

            foreach ($products as $product) {
                $qty = (float) $product['quantity'];
                $rate = (float) ($product['rate'] ?? 0);
                $lineAmount = $qty * $rate;

                $deliveryReceive->items()->create($product);

                InventoryTransaction::query()->create([
                    'store_id' => $deliveryReceive->from_store_id,
                    'doc_type' => 'FGRD',
                    'doc_id' => $deliveryReceive->id,
                    'quantity' => $qty,
                    'rate' => $rate,
                    'amount' => $lineAmount,
                    'date' => $deliveryReceive->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
                InventoryTransaction::query()->create([
                    'store_id' => $deliveryReceive->to_store_id,
                    'doc_type' => 'FGRD',
                    'doc_id' => $deliveryReceive->id,
                    'quantity' => $qty,
                    'rate' => $rate,
                    'amount' => $lineAmount,
                    'date' => $deliveryReceive->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            RequisitionDelivery::where('id', $data['requisition_delivery_id'])->update(['status' => 'received']);

            DB::commit();
            Toastr::success('FG Delivery Receive Entry Successful!.', '', ["progressBar" => true]);
            return redirect()->route('fg-delivery-receives.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
    }

    public function show($id)
    {
        $fgDeliveryReceive = $this->loadDeliveryReceiveForDisplay($id);

        return view('fg_requisition_delivery_receive.show', compact('fgDeliveryReceive'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $receive = DeliveryReceive::findOrFail(decrypt($id));
            InventoryTransaction::where(['doc_id' => $receive->id, 'doc_type' => 'FGRD'])->delete();
            if ($receive->requisition_delivery_id) {
                RequisitionDelivery::where('id', $receive->requisition_delivery_id)
                    ->update(['status' => 'completed']);
            }
            $receive->items()->delete();
            $receive->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('FG Delivery Receive Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('fg-delivery-receives.index');
    }

    public function pdf($id)
    {
        $fgDeliveryReceive = $this->loadDeliveryReceiveForDisplay($id);

        $pdf = Pdf::loadView(
            'fg_requisition_delivery_receive.pdf',
            ['fgDeliveryReceive' => $fgDeliveryReceive],
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

        $label = $fgDeliveryReceive->requisitionDelivery->uid ?? $fgDeliveryReceive->id;

        return $pdf->stream('FGDR-' . $label . '.pdf');
    }

    private function loadDeliveryReceiveForDisplay($id): DeliveryReceive
    {
        return DeliveryReceive::query()
            ->with([
                'toStore:id,name',
                'fromStore:id,name',
                'requisitionDelivery:id,uid,created_by',
                'requisitionDelivery.createdBy:id,name,email',
                'createdBy:id,name,email',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));
    }
}
