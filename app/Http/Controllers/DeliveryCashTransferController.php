<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryCashTransferRequest;
use App\Http\Requests\UpdateDeliveryCashTransferRequest;
use App\Models\ChartOfAccount;
use App\Models\DeliveryCashTransfer;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\OutletAccount;
use App\Models\OutletTransactionConfig;
use App\Models\Sale;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DeliveryCashTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
                $dcTransfers = DeliveryCashTransfer::with('creditAccount', 'debitAccount')->where(['from_outlet' => \auth()->user()->employee->outlet_id])->latest();

            } else {
                $dcTransfers = DeliveryCashTransfer::with('creditAccount', 'debitAccount')->latest();
            }
            return DataTables::of($dcTransfers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('delivery_cash_transfer.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('delivery_cash_transfer.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $exceptToAccountIds = [];
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $cons = OutletAccount::with('coa')->where('outlet_id', \auth()->user()->employee->outlet_id)->get();
            foreach ($cons as $con) {
                $chartOfAccounts[] = $con->coa;
                $exceptToAccountIds[] = $con->coa->id;
            }

            $alreadyTransferred = DeliveryCashTransfer::where('from_outlet', \auth()->user()->employee->outlet_id)->pluck('other_outlet_sale_id')->toArray();
            $othersOutlets = OthersOutletSale::where('payment_status', 'paid')
                ->where('outlet_id', '!=', \auth()->user()->employee->outlet_id)
                ->where('delivery_point_id', \auth()->user()->employee->outlet_id)
                ->whereNotIn('id', $alreadyTransferred)->get();
        } else {
            $chartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->get();
            $othersOutlets = OthersOutletSale::where('payment_status', 'paid')->get();
        }

        $othersOutlets = $othersOutlets->map(function ($item){
            $outlet_accounts = OutletAccount::with('coa')->where('outlet_id', $item->delivery_point_id)->get();
            $ids = $outlet_accounts->pluck('coa_id')->toArray();
            $sale = Sale::where('invoice_number',$item->invoice_number)->with(['accountTransactions'=>function ($q) use ($ids) {
                return $q->whereIn('chart_of_account_id',$ids)->value('amount');
            }])->first();
            $item->paid_account = optional($sale)->accountTransactions->pluck('chart_of_account_id')[0];
            return $item;
        });

        $toChartOfAccounts = ChartOfAccount::where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])->whereNotIn('id', $exceptToAccountIds)->get();

        return view('delivery_cash_transfer.create', compact('othersOutlets', 'chartOfAccounts', 'toChartOfAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeliveryCashTransferRequest $request)
    {
        dd($request->all());
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $otherOutlet = OthersOutletSale::find($request->sale_id);
            $validated['other_outlet_sale_id'] = $otherOutlet->id;
            $validated['invoice_number'] = $otherOutlet->invoice_number;
            $validated['from_outlet'] = $otherOutlet->delivery_point_id;
            $validated['to_outlet'] = $otherOutlet->outlet_id;
            DeliveryCashTransfer::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return $error;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Delivery Cash Transfer Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('delivery-cash-transfers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $deliveryCashTransfer = DeliveryCashTransfer::with('otherOutlet')->findOrFail(decrypt($id));
        return view('delivery_cash_transfer.show', compact('deliveryCashTransfer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryCashTransfer $deliveryCashTransfer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeliveryCashTransferRequest $request, DeliveryCashTransfer $deliveryCashTransfer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            DeliveryCashTransfer::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Delivery Cash Transfer Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('delivery-cash-transfers.index');
    }
}
