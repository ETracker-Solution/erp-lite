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
        if (request()->ajax()) {
            $query = InventoryTransfer::query()
                ->with(['toStore:id,name', 'fromStore:id,name'])
                ->where('type', 'RM');

            $query = $this->filter($query, request());

            return DataTables::of($query->latest())
                ->addIndexColumn()
                ->addColumn('action', fn($row) => view('rm_inventory_transfer.action', compact('row')))
                ->addColumn('created_at', fn($row) => view('common.created_at', compact('row')))
                ->editColumn('status', fn($row) => showStatus($row->status))
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }
        return view('rm_inventory_transfer.index');
    }

    private function filter($query, $request)
    {
        return $query
            ->when($request->date_range, function ($q) use ($request) {
                searchColumnByDateRange($q, 'date', $request->date_range);
            })
            ->when($request->uid, fn($q) => $q->where('uid', 'like', "%{$request->uid}%"))
            ->when($request->from_store, function ($q) use ($request) {
                $q->whereHas('fromStore', function ($sq) use ($request) {
                    $sq->where('name', 'like', "%{$request->from_store}%");
                });
            })
            ->when($request->to_store, function ($q) use ($request) {
                $q->whereHas('toStore', function ($sq) use ($request) {
                    $sq->where('name', 'like', "%{$request->to_store}%");
                });
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status));
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
            'stores' =>  \auth()->user() && \auth()->user()->employee && \auth()->user()->employee->factory_id ? Store::query()->whereType('RM')->where(['doc_type'=>'factory', 'doc_id'=>\auth()->user()->employee->factory_id])->get() : Store::query()->whereType('RM')->get(),
            'to_stores'=>Store::query()->whereType('RM')->get(),
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
        $store = Store::findOrFail($data['from_store_id']);
            $headOffice = false;
             $factory = false;
        if($store->doc_type == 'factory'){
            $factory = true;
        }elseif($store->doc_type == 'ho'){
            $headOffice = true;
        }
        $data['uid'] = generateUniqueUUID($store->doc_id, InventoryTransfer::class, 'uid',$factory,$headOffice);
        $fGInventoryTransfer = InventoryTransfer::create($data);
        foreach ($data['products'] as $product) {
            $fGInventoryTransfer->items()->create($product);
        }
           DB::commit();
       } catch (\Exception $error) {
           DB::rollBack();
           return $error;
           Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
           return back();
       }
        Toastr::success('RM Inventory Transfer Successful!.', '', ["progressBar" => true]);
        return redirect()->route('rm-inventory-transfers.show', $fGInventoryTransfer->id);
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

        if (\request()->has('print')) {
            return view('rm_inventory_transfer.pdf',
                $data);
        }

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
