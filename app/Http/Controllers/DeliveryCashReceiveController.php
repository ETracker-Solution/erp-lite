<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryCashTransferRequest;
use App\Models\DeliveryCashTransfer;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DeliveryCashReceiveController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $outletId = auth()->user()?->employee?->outlet_id;

            $query = DeliveryCashTransfer::query()
                ->select([
                    'delivery_cash_transfers.id',
                    'delivery_cash_transfers.date',
                    'delivery_cash_transfers.invoice_number',
                    'delivery_cash_transfers.amount',
                    'delivery_cash_transfers.status',
                    'delivery_cash_transfers.credit_account_id',
                    'delivery_cash_transfers.debit_account_id',
                    'delivery_cash_transfers.to_outlet',
                    'delivery_cash_transfers.created_at',
                ])
                ->with([
                    'creditAccount:id,name',
                    'debitAccount:id,name',
                ])
                ->when($outletId, fn ($q) => $q->where('to_outlet', $outletId))
                ->latest('delivery_cash_transfers.id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('amount', fn ($row) => number_format((float) $row->amount, 2))
                ->addColumn('action', function ($row) {
                    return view('delivery_cash_receive.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'status', 'created_at'])
                ->make(true);
        }

        return view('delivery_cash_receive.index');
    }

    public function create()
    {
        //
    }

    public function store(StoreDeliveryCashTransferRequest $request)
    {
        //
    }

    public function show($id)
    {
        $deliveryCashTransfer = DeliveryCashTransfer::query()
            ->with([
                'otherOutlet:id,outlet_id,invoice_number,delivery_point_id',
                'otherOutlet.outlet:id,name',
                'creditAccount:id,name',
                'debitAccount:id,name',
            ])
            ->findOrFail(decrypt($id));

        return view('delivery_cash_receive.show', compact('deliveryCashTransfer'));
    }

    public function edit(DeliveryCashTransfer $deliveryCashTransfer)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $deliveryCashTransfer = DeliveryCashTransfer::findOrFail(decrypt($id));

        if ($deliveryCashTransfer->status === 'received') {
            Toastr::info('Already received!.', '', ["progressBar" => true]);

            return redirect()->route('delivery-cash-receives.index');
        }

        DB::beginTransaction();
        try {
            addAccountsTransaction(
                'DCT',
                $deliveryCashTransfer,
                $deliveryCashTransfer->debit_account_id,
                $deliveryCashTransfer->credit_account_id
            );
            $deliveryCashTransfer->update(['status' => 'received']);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::error('Something went wrong!.', '', ["progressBar" => true]);

            return redirect()->route('delivery-cash-receives.index');
        }

        Toastr::success('Delivery Cash Received Successfully!.', '', ["progressBar" => true]);

        return redirect()->route('delivery-cash-receives.index');
    }

    public function destroy($id)
    {
        //
    }
}
