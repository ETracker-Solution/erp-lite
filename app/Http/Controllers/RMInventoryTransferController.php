<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGInventoryTransferRequest;
use App\Http\Requests\StoreRMInventoryTransferRequest;
use App\Http\Requests\UpdateFGInventoryTransferRequest;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransfer;
use App\Models\InventoryTransferItem;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class RMInventoryTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventoryTransfers = InventoryTransfer::with('toStore', 'fromStore')->where(['type' => 'RM'])->latest();
        if (\request()->ajax()) {
            return DataTables::of($inventoryTransfers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('rm_inventory_transfer.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'amount_info'])
                ->make(true);
        }
        return view('rm_inventory_transfer.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serial_count = InventoryTransfer::latest()->first() ? InventoryTransfer::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' => Store::where(['type' => 'RM'])->get(),
            'serial_no' => $serial_no,
        ];
        return view('rm_inventory_transfer.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRMInventoryTransferRequest $request)
    {
        $data = $request->validated();
       DB::beginTransaction();
       try {
        $data['uid'] = generateUniqueUUID($data['from_store_id'], InventoryTransfer::class, 'uid');
        $fGInventoryTransfer = InventoryTransfer::create($data);
        foreach ($data['products'] as $product) {
            $fGInventoryTransfer->items()->create($product);
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $items = InventoryTransferItem::where('inventory_transfer_id', $id)->get();
        $RMInventoryTransfer = InventoryTransfer::with('toStore', 'fromStore')->find($id);
        return view('rm_inventory_transfer.show', compact('RMInventoryTransfer', 'items'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryTransfer $inventoryTransfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFGInventoryTransferRequest $request, InventoryTransfer $inventoryTransfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            InventoryTransfer::findOrFail($id)->delete();
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
        $data = [
            'items' => InventoryTransferItem::where('inventory_transfer_id', $id)->get(),
            'RMInventoryTransfer' => InventoryTransfer::with('toStore', 'fromStore')->find($id),
        ];

        $pdf = PDF::loadView(
            'rm_inventory_transfer.pdf',
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
