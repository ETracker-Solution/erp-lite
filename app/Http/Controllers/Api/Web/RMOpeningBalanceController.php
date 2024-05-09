<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\ROMOpeningBalanceResource;
use App\Models\ChartOfInventory;
use App\Models\Purchase;
use App\Models\RawMaterialOpeningBalance;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RMOpeningBalanceController extends Controller
{
    protected  $base_model;

    public function __construct()
    {
        $this->base_model = RawMaterialOpeningBalance::query();
    }

    public function index()
    {
        $serial_count = $this->base_model->latest()->first() ? $this->base_model->latest()->first()->serial_no : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'serial_no' => $serial_no,
            'stores'=>Store::query()->whereType('RM')->get()

        ];
        return view('opening_balance.raw_materials.index', $data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $serial_count = $this->base_model->latest()->first() ? $this->base_model->latest()->first()->serial_no : 0;
            $serial_no = $serial_count + 1;
            $this->base_model->create([
              'serial_no'=>$serial_no,
              'date'=>$request->date,
              'qty'=>$request->qty,
              'rate'=>$request->rate,
              'amount'=>$request->qty * $request->rate,
              'store_id'=>$request->store_id,
              'coi_id'=>$request->item_id,
              'remarks'=>$request->remarks,
              'created_by'=>auth()->user()->id,
            ]);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Added',
            'success' => true
        ]);
    }

    public function list()
    {
        $rom_balances = $this->base_model->with('chartOfInventory.unit','chartOfInventory.parent')->paginate(10);
        return response()->json(['success' => true, 'items' => new PaginateResource($rom_balances, ROMOpeningBalanceResource::class)]);
    }
}
