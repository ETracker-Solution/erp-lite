<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Http\Resources\PaginateResource;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\Supplier;
use App\Models\SupplierOpeningBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierOpeningBalanceController extends Controller
{
    protected $base_model;

    public function __construct()
    {
        $this->base_model = SupplierOpeningBalance::query();
    }

    public function index()
    {
        return view('opening_balance.supplier.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $alreadyExists = $this->base_model->where(['supplier_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }

            $sob = $this->base_model->create([
                'uid' => getNextId(SupplierOpeningBalance::class),
                'date' => $request->date,
                'amount' => $request->amount,
                'supplier_id' => $request->item_id,
                'remarks' => $request->remarks,
                'created_by' => auth()->user()->id,
            ]);

             addAccountsTransaction('SOB', $sob, getOpeningBalanceOfEquityGLId(), getAccountsPayableGLId());
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
        $sob_balances = $this->base_model->with('supplier')->latest()->paginate(10);
        return response()->json(['success' => true, 'items' => new PaginateResource($sob_balances, CommonResource::class)]);
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $supplierOpeningBalance = SupplierOpeningBalance::find($id);
            $previous_uid = $supplierOpeningBalance->uid;
            AccountTransaction::where(['doc_id' => $supplierOpeningBalance->id, 'doc_type' => 'SOB'])->delete();
            $supplierOpeningBalance->delete();

            $alreadyExists = $this->base_model->where(['supplier_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }
            $sob = $this->base_model->create([
                'uid' => $previous_uid,
                'date' => $request->date,
                'amount' => $request->amount,
                'supplier_id' => $request->item_id,
                'remarks' => $request->remarks,
                'created_by' => auth()->user()->id,
            ]);

            addAccountsTransaction('SOB', $sob, getOpeningBalanceOfEquityGLId(), getAccountsPayableGLId());

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

    public function destroy(string $id)
    {
        try {
            $supplierOpeningBalance = SupplierOpeningBalance::find($id);
            $supplierOpeningBalance->delete();
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
            'next_id' => getNextId(SupplierOpeningBalance::class),
            'suppliers' => Supplier::all(),
            'success' => true
        ]);
    }
}
