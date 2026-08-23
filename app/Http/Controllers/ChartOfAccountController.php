<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Http\Requests\StoreChartOfAccountRequest;
use App\Http\Requests\UpdateChartOfAccountRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     * Tree data is loaded via AJAX (/coa-items) — keep this page shell light.
     */
    public function index()
    {
        return view('chart_of_accounts.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChartOfAccountRequest $request)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required',
        ]);

        $chartOfAccount = ChartOfAccount::query()
            ->select(['id', 'account_type', 'is_bank_cash', 'root_account_type'])
            ->findOrFail($request->input('parent_id'));

        ChartOfAccount::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id'),
            'type' => $request->input('type'),
            'account_type' => $chartOfAccount->account_type,
            'is_bank_cash' => $chartOfAccount->is_bank_cash,
            'root_account_type' => $chartOfAccount->root_account_type,
        ]);

        Toastr::success('Chart of Account Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('chart-of-accounts.index');
    }

    public function show(ChartOfAccount $chartOfAccount)
    {
        //
    }

    public function edit($id)
    {
        $chartOfAccount = ChartOfAccount::findOrFail(decrypt($id));
        $chartOfAccounts = ChartOfAccount::query()
            ->select(['id', 'name', 'type', 'parent_id'])
            ->where('type', 'group')
            ->orderBy('name')
            ->get();

        return view('user.chart_of_account.edit', compact('chartOfAccount', 'chartOfAccounts'));
    }

    public function update(UpdateChartOfAccountRequest $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required',
        ]);

        $parent = ChartOfAccount::query()
            ->select(['id', 'account_type', 'is_bank_cash', 'root_account_type'])
            ->findOrFail($request->input('parent_id'));

        ChartOfAccount::findOrFail(decrypt($id))->update([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id'),
            'type' => $request->input('type'),
            'account_type' => $parent->account_type,
            'is_bank_cash' => $parent->is_bank_cash,
            'root_account_type' => $parent->root_account_type,
            'status' => $request->input('status'),
        ]);

        Toastr::success('Chart of Account Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('chart-of-accounts.index');
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            ChartOfAccount::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Chart of Account Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('chart-of-accounts.index');
    }
}
