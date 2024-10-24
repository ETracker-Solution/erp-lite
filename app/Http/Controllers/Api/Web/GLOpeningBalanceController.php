<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\GLOpeningBalanceResource;
use App\Http\Resources\PaginateResource;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\ChartOfInventory;
use App\Models\GeneralLedgerOpeningBalance;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GLOpeningBalanceController extends Controller
{
    protected $base_model;

    public function __construct()
    {
        $this->base_model = GeneralLedgerOpeningBalance::query();
    }


    public function index()
    {
        return view('opening_balance.general_ledger.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $alreadyExists = $this->base_model->where(['coia_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }

            $account = ChartOfAccount::find($request->item_id);

            $glob = $this->base_model->create([
                'uid' => getNextId(GeneralLedgerOpeningBalance::class),
                'date' => $request->date,
                'amount' => $request->amount,
                'coia_id' => $request->item_id,
                'remarks' => $request->remarks,
                'account_type' => $account->root_account_type,
                'created_by' => auth()->user()->id,
            ]);

            if ($account->root_account_type == 'as') {
                addAccountsTransaction('GLOB', $glob, $account->id, getOpeningBalanceOfEquityGLId());
            }
            if ($account->root_account_type == 'li') {
                addAccountsTransaction('GLOB', $glob, getOpeningBalanceOfEquityGLId(), $account->id);
            }

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
        $glob_balances = $this->base_model->paginate(10);
        return response()->json(['success' => true, 'items' => new PaginateResource($glob_balances, GLOpeningBalanceResource::class)]);
    }

    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            $generalLedgerOpeningBalance = GeneralLedgerOpeningBalance::find($id);
            $previous_uid = $generalLedgerOpeningBalance->uid;
            AccountTransaction::where(['doc_id' => $generalLedgerOpeningBalance->id, 'doc_type' => 'GLOB'])->delete();
            $generalLedgerOpeningBalance->delete();

            $alreadyExists = $this->base_model->where(['coia_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }
            $account = ChartOfAccount::find($request->item_id);

            $glob = $this->base_model->create([
                'uid' => $previous_uid,
                'date' => $request->date,
                'amount' => $request->amount,
                'coia_id' => $request->item_id,
                'remarks' => $request->remarks,
                'account_type' => $account->root_account_type,
                'created_by' => auth()->user()->id,
            ]);

            addAccountsTransaction('GLOB', $glob, getFGInventoryGLId(), getOpeningBalanceOfEquityGLId());

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
            $generalLedgerOpeningBalance = GeneralLedgerOpeningBalance::find($id);
            AccountTransaction::where(['doc_id' => $generalLedgerOpeningBalance->id, 'doc_type' => 'GLOB'])->delete();
            $generalLedgerOpeningBalance->delete();
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
            'next_id' => getNextId(GeneralLedgerOpeningBalance::class),
            'accounts' => ChartOfAccount::where(['type' => 'ledger'])->whereIN('root_account_type', ['as', 'li'])->get(),
            'success' => true
        ]);
    }
}
