<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\ROMOpeningBalanceResource;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\Purchase;
use App\Models\RawMaterialOpeningBalance;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RMOpeningBalanceController extends Controller
{
    protected $base_model;

    public function __construct()
    {
        $this->base_model = RawMaterialOpeningBalance::query();
    }

    public function index()
    {
        return view('opening_balance.raw_materials.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $alreadyExists = $this->base_model->where(['store_id' => $request->store_id, 'coi_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }

            $rmob = $this->base_model->create([
                'uid' => getNextId(RawMaterialOpeningBalance::class),
                'date' => $request->date,
                'quantity' => $request->qty,
                'rate' => $request->rate,
                'amount' => $request->qty * $request->rate,
                'store_id' => $request->store_id,
                'coi_id' => $request->item_id,
                'remarks' => $request->remarks,
                'created_by' => auth()->user()->id,
            ]);
            addInventoryTransaction(1, 'RMOB', $rmob);

            addAccountsTransaction('RMOB', $rmob, getRMInventoryGLId(), getOpeningBalanceOfEquityGLId());
            DB::commit();
        } catch (\Exception $exception) {
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
        $rom_balances = $this->base_model->with('chartOfInventory.unit', 'chartOfInventory.parent')->paginate(10);
        return response()->json(['success' => true, 'items' => new PaginateResource($rom_balances, ROMOpeningBalanceResource::class)]);
    }

    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $rawMaterialOpeningBalance = RawMaterialOpeningBalance::find($id);
            $previous_uid = $rawMaterialOpeningBalance->uid;
            $rawMaterialOpeningBalance->inventoryTransaction()->delete();
            $rawMaterialOpeningBalance->delete();

            $alreadyExists = $this->base_model->where(['store_id' => $request->store_id, 'coi_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }

            $rmob = $this->base_model->create([
                'uid' => $previous_uid,
                'date' => $request->date,
                'quantity' => $request->qty,
                'rate' => $request->rate,
                'amount' => $request->qty * $request->rate,
                'store_id' => $request->store_id,
                'coi_id' => $request->item_id,
                'remarks' => $request->remarks,
                'created_by' => auth()->user()->id,
            ]);

            addInventoryTransaction(1, 'RMOB', $rmob);

            addAccountsTransaction('RMOB', $rmob, getRMInventoryGLId(), getOpeningBalanceOfEquityGLId());

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Update',
            'success' => true
        ]);
    }

    public function destroy($id)
    {
        try {
            $rawMaterialOpeningBalance = RawMaterialOpeningBalance::find($id);
            $rawMaterialOpeningBalance->inventoryTransaction()->delete();
            $rawMaterialOpeningBalance->delete();
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Update',
            'success' => true
        ]);
    }

    public function initialInfo()
    {
        return response()->json([
            'next_id' => getNextId(RawMaterialOpeningBalance::class),
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' => Store::query()->whereType('RM')->get(),
            'success' => true
        ]);
    }
}
