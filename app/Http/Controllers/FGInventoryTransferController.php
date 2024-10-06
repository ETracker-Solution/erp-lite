<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFGInventoryTransferRequest;
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

class FGInventoryTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (\request()->ajax()) {
            if (!auth()->user()->is_super) {
                if (auth()->user()->employee){
                    if (auth()->user()->employee->factory_id){
                        $fGInventoryTransfers = InventoryTransfer::with('toStore', 'fromStore')
                            ->where(['type' => 'FG'])->whereIn('from_store_id', auth()->user()->employee->factory->stores()->pluck('id')->toArray())->latest();
                    }
                    if (auth()->user()->employee->outlet_id){
                        $fGInventoryTransfers = InventoryTransfer::with('toStore', 'fromStore')
                            ->where(['type' => 'FG'])->whereIn('from_store_id', auth()->user()->employee->outlet->stores()->pluck('id')->toArray())->latest();
                    }
                }
            }else{
                $fGInventoryTransfers = InventoryTransfer::with('toStore', 'fromStore')->where(['type' => 'FG'])->latest();
            }
            return DataTables::of($fGInventoryTransfers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('fg_inventory_transfer.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'amount_info'])
                ->make(true);
        }
        return view('fg_inventory_transfer.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $stores = Store::query()->whereType('FG')->where(['doc_type'=>'outlet','status'=>'active','doc_id'=>\auth()->user()->employee->outlet_id])->get();
            $mystores = Store::query()->whereType('FG')->where(['doc_type'=>'outlet','doc_id'=>\auth()->user()->employee->outlet_id])->pluck('id')->toArray();
        } elseif(\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->factory_id) {
            $stores = Store::query()->whereType('FG')->where(['doc_type'=>'factory','status'=>'active','doc_id'=>\auth()->user()->employee->factory_id])->get();
        }else{
            $stores = Store::query()->whereType('FG')->where('status','active')->get();
        }


        $serial_count = InventoryTransfer::latest()->first() ? InventoryTransfer::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => $stores,
            'to_stores' => \auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id ? Store::query()->whereType('FG')->where(['status'=>'active'])->whereNotIn('id',$mystores)->get() : Store::query()->whereType('FG')->where('status','active')->get(),
            'serial_no' => $serial_no,
        ];
        return view('fg_inventory_transfer.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFGInventoryTransferRequest $request)
    {
        $data = $request->validated();
//        DB::beginTransaction();
//        try {
        $data['uid'] = generateUniqueUUID($data['from_store_id'],InventoryTransfer::class,'uid');
        $fGInventoryTransfer = InventoryTransfer::create($data);
        foreach ($data['products'] as $product) {
            $fGInventoryTransfer->items()->create($product);
        }
//            DB::commit();
//        } catch (\Exception $error) {
//            DB::rollBack();
//            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
//            return back();
//        }
        Toastr::success('FG Inventory Transfer Successful!.', '', ["progressBar" => true]);
        return redirect()->route('fg-inventory-transfers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $items = InventoryTransferItem::where('inventory_transfer_id', decrypt($id))->get();
        $fGInventoryTransfer = InventoryTransfer::with('toStore', 'fromStore')->find(decrypt($id));
        return view('fg_inventory_transfer.show', compact('fGInventoryTransfer', 'items'));
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
        Toastr::success('FG Inventory Transfer Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('fg-inventory-transfers.index');
    }

    public function pdfDownload($id)
    {
        $data = [
            'items' => InventoryTransferItem::where('inventory_transfer_id',decrypt($id))->get(),
            'FGInventoryTransfer' => InventoryTransfer::with('toStore', 'fromStore')->find(decrypt($id)),
        ];

        $pdf = PDF::loadView(
            'fg_inventory_transfer.pdf',
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
