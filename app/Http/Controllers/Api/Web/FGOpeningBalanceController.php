<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\FGOpeningBalanceResource;
use App\Http\Resources\PaginateResource;
use App\Models\AccountTransaction;
use App\Models\ChartOfInventory;
use App\Models\FinishGoodsOpeningBalance;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FGOpeningBalanceController extends Controller
{
    protected $base_model;

    public function __construct()
    {
        $this->base_model = FinishGoodsOpeningBalance::query();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('opening_balance.finish_goods.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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
                'uid' => getNextId(FinishGoodsOpeningBalance::class),
                'date' => $request->date,
                'quantity' => $request->qty,
                'rate' => $request->rate,
                'amount' => $request->qty * $request->rate,
                'store_id' => $request->store_id,
                'coi_id' => $request->item_id,
                'remarks' => $request->remarks,
                'created_by' => auth()->user()->id,
            ]);
            addInventoryTransaction(1, 'FGOB', $rmob);

            addAccountsTransaction('FGOB', $rmob, getFGInventoryGLId(), getOpeningBalanceOfEquityGLId());
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
        $fgob_balances = $this->base_model->with('chartOfInventory.unit', 'chartOfInventory.parent')->paginate(10);
        return response()->json(['success' => true, 'items' => new PaginateResource($fgob_balances, FGOpeningBalanceResource::class)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        DB::beginTransaction();
        try {
            $finishGoodsOpeningBalance = FinishGoodsOpeningBalance::find($id);
            $previous_uid = $finishGoodsOpeningBalance->uid;
            AccountTransaction::where(['doc_id' => $finishGoodsOpeningBalance->id, 'doc_type' => 'FGOB'])->delete();
            $finishGoodsOpeningBalance->inventoryTransaction()->delete();

            $finishGoodsOpeningBalance->delete();

            $alreadyExists = $this->base_model->where(['store_id' => $request->store_id, 'coi_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }

            $fgob = $this->base_model->create([
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

            addInventoryTransaction(1, 'FGOB', $fgob);


            addAccountsTransaction('FGOB', $fgob, getFGInventoryGLId(), getOpeningBalanceOfEquityGLId());

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $finishGoodsOpeningBalance = FinishGoodsOpeningBalance::find($id);
            $finishGoodsOpeningBalance->inventoryTransaction()->delete();
            $finishGoodsOpeningBalance->delete();
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
            'next_id' => getNextId(FinishGoodsOpeningBalance::class),
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => \auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id ? Store::query()->whereType('FG')->where(['doc_type'=>'outlet', 'doc_id'=>\auth()->user()->employee->outlet_id])->get() : Store::query()->whereType('FG')->get(),
            'success' => true
        ]);
    }
}
