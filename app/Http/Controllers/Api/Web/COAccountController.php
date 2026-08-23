<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;

class COAccountController extends Controller
{
    public function items()
    {
        $flat = ChartOfAccount::query()
            ->select([
                'id', 'name', 'type', 'parent_id', 'account_type',
                'status', 'is_bank_cash', 'default_type',
            ])
            ->orderByRaw("FIELD(type, 'group', 'ledger')")
            ->orderBy('name')
            ->get();

        $allChartOfAccounts = nestByParentId($flat, 'childrens');

        return view('chart_of_accounts.items', compact('allChartOfAccounts'));
    }

    public function details($id)
    {
        $account = ChartOfAccount::query()
            ->select([
                'id', 'name', 'type', 'parent_id', 'account_type',
                'is_bank_cash', 'default_type', 'status',
            ])
            ->with('parent:id,name')
            ->findOrFail($id);

        return response()->json([
            'item_id' => $account->id,
            'item_name' => $account->name,
            'item_type' => $account->type,
            'group_name' => $account->parent?->name ?? '',
            'account_type' => $account->account_type,
            'is_bank_cash' => $account->is_bank_cash,
            'default_type' => $account->default_type,
            'status' => $account->status,
            'is_payment_method' => $account->type === 'group' && $account->default_type === 'payment_method',
        ]);
    }

    public function update($id)
    {
        try {
            $account = ChartOfAccount::query()->findOrFail($id);

            if (request()->filled('item_name')) {
                $account->name = request()->item_name;
            }

            if (request()->has('is_bank_cash') && in_array(request()->is_bank_cash, ['yes', 'no'], true)) {
                $account->is_bank_cash = request()->is_bank_cash;
            }

            if ($account->type === 'group' && request()->has('is_payment_method')) {
                if (request()->boolean('is_payment_method')) {
                    $account->default_type = 'payment_method';
                    $account->is_bank_cash = 'yes';
                } elseif ($account->default_type === 'payment_method') {
                    $account->default_type = null;
                }
            } elseif (request()->has('default_type')) {
                $defaultType = request()->default_type;
                $account->default_type = ($defaultType === '' || $defaultType === null)
                    ? null
                    : $defaultType;
            }

            $account->updated_by = auth()->id();
            $account->save();

            $message = 'Updated';
            if (
                $account->type === 'group'
                && $account->default_type === 'payment_method'
                && request()->boolean('provision_everywhere')
            ) {
                $prov = provisionPaymentMethodEverywhere($account->name, [
                    'outlets' => request()->boolean('provision_outlets', true),
                    'office' => request()->boolean('provision_office', true),
                ]);
                $message = $this->provisionMessage('Updated', $prov);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false,
            ]);
        }

        return response()->json([
            'message' => $message ?? 'Updated',
            'success' => true,
        ]);
    }

    public function store($id)
    {
        try {
            DB::beginTransaction();

            $parent = ChartOfAccount::query()->findOrFail($id);
            $itemType = request()->item_type;
            $isPaymentMethod = $itemType === 'group' && request()->boolean('is_payment_method');

            $child = $parent->subChartOfAccounts()->create([
                'name' => request()->item_name,
                'type' => $itemType,
                'account_type' => $parent->account_type,
                'is_bank_cash' => $isPaymentMethod ? 'yes' : $parent->is_bank_cash,
                'root_account_type' => $parent->root_account_type,
                'default_type' => $isPaymentMethod
                    ? 'payment_method'
                    : (request()->default_type ?: null),
                'created_by' => auth()->id(),
            ]);

            $message = 'Added';
            if ($isPaymentMethod && request()->boolean('provision_everywhere')) {
                $prov = provisionPaymentMethodEverywhere($child->name, [
                    'outlets' => request()->boolean('provision_outlets', true),
                    'office' => request()->boolean('provision_office', true),
                ]);
                $message = $this->provisionMessage('Added', $prov);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false,
            ]);
        }

        return response()->json([
            'message' => $message,
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

    private function provisionMessage(string $prefix, array $prov): string
    {
        $parts = [
            $prefix,
            "{$prov['outlets']} outlet(s)",
            "{$prov['ledgers']} ledger(s)",
            "{$prov['otc']} OTC",
        ];
        if (!empty($prov['office_ledger'])) {
            $parts[] = 'office ledger';
        }

        return implode(' · ', $parts);
    }
}
