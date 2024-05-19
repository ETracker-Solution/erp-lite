<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommonResource;
use App\Http\Resources\PaginateResource;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\CustomerOpeningBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerOpeningBalanceController extends Controller
{
    protected $base_model;

    public function __construct()
    {
        $this->base_model = CustomerOpeningBalance::query();
    }

    public function index()
    {
        return view('opening_balance.customer.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $alreadyExists = $this->base_model->where(['customer_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }

            $account = ChartOfAccount::find($request->item_id);

            $cob = $this->base_model->create([
                'uid' => getNextId(CustomerOpeningBalance::class),
                'date' => $request->date,
                'amount' => $request->amount,
                'customer_id' => $request->item_id,
                'remarks' => $request->remarks,
                'created_by' => auth()->user()->id,
            ]);

             addAccountsTransaction('COB', $cob, getAccountsReceiveableGLId(), getOpeningBalanceOfEquityGLId());
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
        $cob_balances = $this->base_model->with('customer')->paginate(10);
        return response()->json(['success' => true, 'items' => new PaginateResource($cob_balances, CommonResource::class)]);
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $customerOpeningBalance = CustomerOpeningBalance::find($id);
            $previous_uid = $customerOpeningBalance->uid;
            AccountTransaction::where(['doc_id' => $customerOpeningBalance->id, 'doc_type' => 'COB'])->delete();
            $customerOpeningBalance->delete();

            $alreadyExists = $this->base_model->where(['customer_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }

            $cob = $this->base_model->create([
                'uid' => $previous_uid,
                'date' => $request->date,
                'amount' => $request->amount,
                'customer_id' => $request->item_id,
                'remarks' => $request->remarks,
                'created_by' => auth()->user()->id,
            ]);

            addAccountsTransaction('COB', $cob, getAccountsReceiveableGLId(), getOpeningBalanceOfEquityGLId());

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
            $customerOpeningBalance = CustomerOpeningBalance::find($id);
            $customerOpeningBalance->delete();
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
            'next_id' => getNextId(CustomerOpeningBalance::class),
            'customers' => Customer::all(),
            'success' => true
        ]);
    }
}
