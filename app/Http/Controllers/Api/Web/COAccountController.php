<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;

class COAccountController extends Controller
{
    public function items()
    {
        $flat = ChartOfAccount::query()
            ->select(['id', 'name', 'type', 'parent_id', 'account_type', 'status'])
            ->orderByRaw("FIELD(type, 'group', 'ledger')")
            ->orderBy('name')
            ->get();

        $allChartOfAccounts = nestByParentId($flat, 'childrens');

        return view('chart_of_accounts.items', compact('allChartOfAccounts'));
    }

    public function details($id)
    {
        $account = ChartOfAccount::query()
            ->select(['id', 'name', 'type', 'parent_id', 'account_type'])
            ->with('parent:id,name')
            ->findOrFail($id);

        return response()->json([
            'item_id' => $account->id,
            'item_name' => $account->name,
            'item_type' => $account->type,
            'group_name' => $account->parent?->name ?? '',
            'account_type' => $account->account_type,
        ]);
    }

    public function update($id)
    {
        try {
            $account = ChartOfAccount::query()->findOrFail($id);
            if (request()->filled('item_name')) {
                $account->name = request()->item_name;
            }
            $account->updated_by = auth()->id();
            $account->save();
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false,
            ]);
        }

        return response()->json([
            'message' => 'Updated',
            'success' => true,
        ]);
    }

    public function store($id)
    {
        try {
            $account = ChartOfAccount::query()->findOrFail($id);
            $account->subChartOfAccounts()->create([
                'name' => request()->item_name,
                'type' => request()->item_type,
                'account_type' => $account->account_type,
                'is_bank_cash' => $account->is_bank_cash,
                'root_account_type' => $account->root_account_type,
                'created_by' => auth()->id(),
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false,
            ]);
        }

        return response()->json([
            'message' => 'Added',
            'success' => true,
        ]);
    }

    public function delete($id)
    {
        return response()->json([
            'message' => 'Deleting Account is not allowed from now',
            'success' => false,
        ]);
    }
}
