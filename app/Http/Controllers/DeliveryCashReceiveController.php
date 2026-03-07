<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryCashTransferRequest;
use App\Http\Requests\UpdateDeliveryCashTransferRequest;
use App\Models\ChartOfAccount;
use App\Models\DeliveryCashTransfer;
use App\Models\OthersOutletSale;
use App\Models\OutletTransactionConfig;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DeliveryCashReceiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = DeliveryCashTransfer::query()
                ->with(['creditAccount:id,name', 'debitAccount:id,name']);

            if (auth()->user() && auth()->user()->employee && auth()->user()->employee->outlet_id) {
                $query->where('to_outlet', auth()->user()->employee->outlet_id);
            }

            $query = $this->filter($query, request());

            return DataTables::of($query->latest())
                ->addIndexColumn()
                ->addColumn('action', fn($row) => view('delivery_cash_receive.action-button', compact('row')))
                ->addColumn('created_at', fn($row) => view('common.created_at', compact('row')))
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('delivery_cash_receive.index');
    }

    private function filter($query, $request)
    {
        return $query
            ->when($request->date_range, function ($q) use ($request) {
                searchColumnByDateRange($q, 'date', $request->date_range);
            })
            ->when($request->invoice_number, fn($q) => $q->where('invoice_number', 'like', "%{$request->invoice_number}%"))
            ->when($request->from_account, function ($q) use ($request) {
                $q->whereHas('creditAccount', function ($sq) use ($request) {
                    $sq->where('name', 'like', "%{$request->from_account}%");
                });
            })
            ->when($request->to_account, function ($q) use ($request) {
                $q->whereHas('debitAccount', function ($sq) use ($request) {
                    $sq->where('name', 'like', "%{$request->to_account}%");
                });
            });
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
    public function store(StoreDeliveryCashTransferRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $deliveryCashTransfer = DeliveryCashTransfer::with('otherOutlet')->findOrFail(decrypt($id));
        return view('delivery_cash_receive.show', compact('deliveryCashTransfer'));
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
    public function update(Request $request, $id )
    {
        $deliveryCashTransfer = DeliveryCashTransfer::find(decrypt($id));
        try {
            if($deliveryCashTransfer->status !== 'received'){
                addAccountsTransaction('DCT', $deliveryCashTransfer, $deliveryCashTransfer->debit_account_id, $deliveryCashTransfer->credit_account_id);
                $deliveryCashTransfer->update([
                    'status'=>'received'
                ]);
            }
        }catch (\Exception $exception){
            Toastr::error('Something went wrong!.', '', ["progressBar" => true]);
            return redirect()->route('delivery-cash-receives.index');
        }
        Toastr::success('Delivery Cash Received Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('delivery-cash-receives.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
