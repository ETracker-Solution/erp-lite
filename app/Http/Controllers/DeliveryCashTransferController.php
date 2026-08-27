<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryCashTransferRequest;
use App\Http\Requests\UpdateDeliveryCashTransferRequest;
use App\Models\AccountTransaction;
use App\Models\ChartOfAccount;
use App\Models\DeliveryCashTransfer;
use App\Models\OthersOutletSale;
use App\Models\OutletAccount;
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
                    'delivery_cash_transfers.from_outlet',
                    'delivery_cash_transfers.created_at',
                ])
                ->with([
                    'creditAccount:id,name',
                    'debitAccount:id,name',
                ])
                ->when($outletId, fn ($q) => $q->where('from_outlet', $outletId))
                ->latest('delivery_cash_transfers.id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('amount', fn ($row) => number_format((float) $row->amount, 2))
                ->addColumn('action', function ($row) {
                    return view('delivery_cash_transfer.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'status', 'created_at'])
                ->make(true);
        }

        return view('delivery_cash_transfer.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outletId = auth()->user()?->employee?->outlet_id;
        $exceptToAccountIds = [];
        $chartOfAccounts = collect();

        if ($outletId) {
            $outletAccounts = OutletAccount::query()
                ->with('coa:id,name')
                ->where('outlet_id', $outletId)
                ->get(['id', 'outlet_id', 'coa_id']);

            $chartOfAccounts = $outletAccounts->pluck('coa')->filter()->values();
            $exceptToAccountIds = $outletAccounts->pluck('coa_id')->filter()->values()->all();
        } else {
            $chartOfAccounts = ChartOfAccount::query()
                ->where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        $toChartOfAccounts = ChartOfAccount::query()
            ->where(['is_bank_cash' => 'yes', 'type' => 'ledger', 'status' => 'active'])
            ->when(!empty($exceptToAccountIds), fn ($q) => $q->whereNotIn('id', $exceptToAccountIds))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('delivery_cash_transfer.create', compact('chartOfAccounts', 'toChartOfAccounts'));
    }

    /**
     * Select2 search for transferable others-outlet invoices.
     */
    public function searchInvoices()
    {
        $outletId = auth()->user()?->employee?->outlet_id;
        $term = trim((string) request('q', ''));

        $query = OthersOutletSale::query()
            ->select(['id', 'invoice_number'])
            ->where('payment_status', 'paid');

        if ($outletId) {
            $alreadyTransferred = DeliveryCashTransfer::query()
                ->where('from_outlet', $outletId)
                ->whereNotNull('other_outlet_sale_id')
                ->pluck('other_outlet_sale_id');

            $query->where('outlet_id', '!=', $outletId)
                ->where('delivery_point_id', $outletId)
                ->whereNotIn('id', $alreadyTransferred);
        }

        if ($term !== '') {
            $query->where('invoice_number', 'like', '%' . $term . '%');
        }

        $rows = $query->latest('id')->limit(20)->get();

        return response()->json([
            'results' => $rows->map(fn ($row) => [
                'id' => $row->id,
                'text' => $row->invoice_number,
            ]),
        ]);
    }

    /**
     * Load amount + paid credit account for one invoice (on select).
     */
    public function invoiceDetails($id)
    {
        $outletId = auth()->user()?->employee?->outlet_id;

        $sale = OthersOutletSale::query()
            ->select([
                'id',
                'invoice_number',
                'outlet_id',
                'delivery_point_id',
                'delivery_point_receive_amount',
                'payment_status',
            ])
            ->where('payment_status', 'paid')
            ->findOrFail($id);

        if ($outletId) {
            abort_unless(
                (int) $sale->delivery_point_id === (int) $outletId
                && (int) $sale->outlet_id !== (int) $outletId,
                403
            );
        }

        return response()->json([
            'id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'delivery_point_receive_amount' => $sale->delivery_point_receive_amount,
            'paid_account' => $this->resolvePaidAccount($sale),
        ]);
    }

    /**
     * Resolve paid COA for a single others-outlet sale.
     */
    private function resolvePaidAccount(OthersOutletSale $item): ?int
    {
        $coaIds = OutletAccount::query()
            ->where('outlet_id', $item->delivery_point_id)
            ->pluck('coa_id')
            ->filter()
            ->values();

        if ($coaIds->isEmpty()) {
            return null;
        }

        $sale = Sale::query()
            ->where('invoice_number', $item->invoice_number)
            ->first(['id']);

        if (!$sale) {
            return null;
        }

        return AccountTransaction::query()
            ->where('doc_type', 'POS')
            ->where('doc_id', $sale->id)
            ->whereIn('chart_of_account_id', $coaIds)
            ->value('chart_of_account_id');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeliveryCashTransferRequest $request)
    {
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
        $deliveryCashTransfer = DeliveryCashTransfer::query()
            ->with([
                'otherOutlet:id,outlet_id,invoice_number,delivery_point_id',
                'otherOutlet.outlet:id,name',
                'creditAccount:id,name',
                'debitAccount:id,name',
            ])
            ->findOrFail(decrypt($id));

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
