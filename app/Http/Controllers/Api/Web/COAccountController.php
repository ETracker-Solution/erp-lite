<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\ChartOfInventory;
use Illuminate\Http\Request;

class COAccountController extends Controller
{
    protected $base_model;
    public function __construct()
    {
        $this->base_model = ChartOfAccount::query();
    }

    public function items()
    {
        $allChartOfAccounts = $this->base_model->whereNull('parent_id')->get();
        return view('chart_of_accounts.items', compact('allChartOfAccounts',));
    }

    public function details($id)
    {
        $account = $this->base_model->with('parent')->find($id);
        $data = [
            'item_id' => $account->id,
            'item_name' => $account->name,
            'item_type' => $account->type,
            'group_name' => $account->parent_id && $account->parent ? $account->parent->name : '',
            'account_type' => $account->account_type,
        ];
        return response()->json($data);
    }

    public function update($id)
    {
        try {
            $account = $this->base_model->find($id);
            if (\request()->filled('item_name')) {
                $account->name = \request()->item_name;
            }
            $account->updated_by = auth()->user()->id;
            $account->update();
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Updated',
            'success' => true
        ]);
    }

    public function store($id)
    {
        try {
            $account = $this->base_model->find($id);
            $account->subChartOfAccounts()->create([
                'name' => \request()->item_name,
                'type' => \request()->item_type,
                'account_type' => $account->account_type,
                'created_by' => auth()->user()->id,
            ]);
        } catch (\Exception $exception) {
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

    public function delete($id)
    {
        try {
            $account = $this->base_model->find($id);
            $account->delete();
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Deleted',
            'success' => true
        ]);
    }
}
