<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Support\Str;
use App\Http\Requests\StoreChartOfAccountRequest;
use App\Http\Requests\UpdateChartOfAccountRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class ChartOfAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allChartOfAccounts = ChartOfAccount::whereNull('parent_id')->get();
        $groups = ChartOfAccount::where('type','group')->get();
        return view('chart_of_accounts.index', compact('allChartOfAccounts','groups'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreChartOfAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreChartOfAccountRequest $request)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required',


        ]);
        $chartOfAccount = ChartOfAccount::find($request->input('parent_id'));
        ChartOfAccount::create([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id'),
            'type' => $request->input('type'),
            'account_type' => $chartOfAccount->account_type,
            'is_bank_cash' => $chartOfAccount->is_bank_cash,
            'parent_account_type' => $chartOfAccount->parent_account_type,
        ]);
        Toastr::success('Chart of Account Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.chart-of-accounts.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Http\Response
     */
    public function show(ChartOfAccount $chartOfAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $allChartOfAccounts = ChartOfAccount::whereNull('parent_id')->get();
        $chartOfAccount = ChartOfAccount::findOrFail(decrypt($id));
        $chartOfAccounts = ChartOfAccount::where('type','group')->get();
        return view('user.chart_of_account.edit', compact('chartOfAccount','chartOfAccounts','allChartOfAccounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateChartOfAccountRequest  $request
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateChartOfAccountRequest $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'parent_id' => 'required',
        ]);

        $chartOfAccount = ChartOfAccount::find($request->input('parent_id'));
        ChartOfAccount::findOrFail(decrypt($id))->update([
            'name' => $request->input('name'),
            'parent_id' => $request->input('parent_id'),
            'type' => $request->input('type'),
            'account_type' => $chartOfAccount->account_type,
            'is_bank_cash' => $chartOfAccount->is_bank_cash,
            'parent_account_type' => $chartOfAccount->parent_account_type,
            'status' => $request->input('status')
        ]);
        Toastr::success('Chart of Account Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('user.chart-of-accounts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ChartOfAccount  $chartOfAccount
     * @return \Illuminate\Http\Response
     */
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
        return redirect()->route('user.chart-of-accounts.index');
    }
}
