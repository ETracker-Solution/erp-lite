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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $data = TransferReceive::with('fromStore', 'toStore', 'inventoryTransfer')->where('type', 'RM')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('rm_inventory_transfer_receive.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }
        return view('rm_inventory_transfer_receive.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->factory_id) {
            $inventory_transfers = InventoryTransfer::whereHas('toStore', function ($query) {
                $query->where(['doc_type' => 'factory', 'doc_id' => \auth()->user()->employee->factory_id]);
            })->where(['type' => 'RM', 'status' => 'pending'])->get();
        } else {
            $inventory_transfers = InventoryTransfer::where(['type' => 'RM', 'status' => 'pending'])->get();
        }
        $data = [
            'from_stores' => Store::where(['type' => 'RM'])->get(),
            'to_stores' => Store::where(['type' => 'RM'])->get(),
            'inventory_transfers' => $inventory_transfers
        ];
        return view('rm_inventory_transfer_receive.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRMTransferReceiveRequest $request)
    {
        try {
            
            DB::beginTransaction();
            $data = $request->validated();
            $store = Store::find($data['to_store_id']);
            $headOffice = false;
            $factory = false;
            if($store->doc_type == 'factory'){
                $factory = true;
            }elseif($store->doc_type == 'ho'){
                $headOffice = true;
            }
            $data['uid'] = generateUniqueUUID($store->doc_id, TransferReceive::class, 'uid',$factory,$headOffice);
            $inventoryTransfer = TransferReceive::query()->create($data);
            $products = $request->get('products');
            foreach ($products as $product) {
                $inventoryTransfer->items()->create($product);

                // Inventory Transaction Effect
                InventoryTransaction::query()->create([
                    'store_id' => $inventoryTransfer->from_store_id,
                    'doc_type' => 'RMIT',
                    'doc_id' => $inventoryTransfer->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $inventoryTransfer->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
                InventoryTransaction::query()->create([
                    'store_id' => $inventoryTransfer->to_store_id,
                    'doc_type' => 'RMIT',
                    'doc_id' => $inventoryTransfer->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $inventoryTransfer->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }
            InventoryTransfer::where('id', $data['inventory_transfer_id'])->update(['status' => 'received']);
            DB::commit();
            Toastr::success('RM Transfer Receive Entry Successful!.', '', ["progressBar" => true]);
            return redirect()->route('rm-transfer-receives.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressbar" => true]);
            return back();
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rmTransferReceive = TransferReceive::with('toStore', 'fromStore')->find(decrypt($id));
        return view('rm_inventory_transfer_receive.show', compact('rmTransferReceive'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function pdf($id)
    {
        $data = [
            'rmTransferReceive' => TransferReceive::with('toStore', 'fromStore')->find(decrypt($id)),
        ];

        $pdf = PDF::loadView(
            'rm_inventory_transfer_receive.pdf',
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
