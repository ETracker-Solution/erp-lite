<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Http\Requests\StorePurchaseReturnRequest;
use App\Http\Requests\UpdatePurchaseReturnRequest;
use App\Models\Store;
use App\Models\SupplierGroup;
use App\Models\SupplierTransaction;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = PurchaseReturn::query()
                ->select([
                    'purchase_returns.id',
                    'purchase_returns.uid',
                    'purchase_returns.date',
                    'purchase_returns.status',
                    'purchase_returns.subtotal',
                    'purchase_returns.vat',
                    'purchase_returns.net_payable',
                    'purchase_returns.supplier_id',
                    'purchase_returns.store_id',
                    'purchase_returns.purchase_id',
                    'purchase_returns.created_at',
                ])
                ->with([
                    'supplier:id,name',
                    'store:id,name',
                    'purchase:id,uid',
                ])
                ->latest('id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('subtotal', fn ($row) => number_format((float) $row->subtotal, 2))
                ->editColumn('vat', fn ($row) => number_format((float) $row->vat, 2))
                ->editColumn('net_payable', fn ($row) => number_format((float) $row->net_payable, 2))
                ->addColumn('action', function ($row) {
                    return view('purchase_return.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['status', 'action', 'created_at'])
                ->make(true);
        }

        return view('purchase_return.index');
    }

    public function create()
    {
        $prefillPurchaseId = request()->integer('purchase_id') ?: null;

        return view('purchase_return.create', [
            'supplier_groups' => SupplierGroup::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name']),
            'stores' => Store::query()
                ->where(['type' => 'RM', 'doc_type' => 'ho', 'doc_id' => null])
                ->orderBy('name')
                ->get(['id', 'name']),
            'purchases' => Purchase::query()
                ->select(['id', 'uid', 'supplier_id', 'store_id', 'status'])
                ->where('type', 'rm')
                ->where('status', '!=', 'returned')
                ->latest('id')
                ->get(),
            'prefillPurchaseId' => $prefillPurchaseId,
        ]);
    }

    public function store(StorePurchaseReturnRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();
        try {
            if (count($data['products']) < 1) {
                DB::rollBack();
                Toastr::info('At Least One Product Required.', '', ['progressBar' => true]);

                return back();
            }
            $purchase_return = PurchaseReturn::query()->create($data);
            Purchase::where('id', $data['purchase_id'])->update(['status' => 'returned']);
            $purchase_return->amount = $purchase_return->net_payable;
            foreach ($data['products'] as $product) {
                $purchase_return->items()->create($product);
                InventoryTransaction::query()->create([
                    'store_id' => $purchase_return->store_id,
                    'doc_type' => 'GPBR',
                    'doc_id' => $purchase_return->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $purchase_return->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
            }

            addAccountsTransaction('GPB', $purchase_return, getAccountsPayableGLId(), getRMInventoryGLId());

            SupplierTransaction::query()->create([
                'supplier_id' => $purchase_return->supplier_id,
                'doc_type' => 'GPB',
                'doc_id' => $purchase_return->id,
                'amount' => $purchase_return->net_payable,
                'date' => $purchase_return->date,
                'transaction_type' => -1,
                'chart_of_account_id' => getAccountsPayableGLId(),
                'description' => 'Purchase of goods',
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ['progressBar' => true]);

            return back();
        }
        Toastr::success('Purchase Return Created Successfully!.', '', ['progressBar' => true]);

        return redirect()->route('purchase-returns.index');
    }

    public function show($id)
    {
        $purchaseReturn = PurchaseReturn::query()
            ->with([
                'supplier:id,name,address,mobile',
                'store:id,name',
                'purchase:id,uid,date',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        return view('purchase_return.show', compact('purchaseReturn'));
    }

    public function pdfDownload($id)
    {
        $purchaseReturn = PurchaseReturn::query()
            ->with([
                'supplier:id,name,address,mobile',
                'store:id,name',
                'purchase:id,uid',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        $pdf = PDF::loadView(
            'purchase_return.pdf',
            ['model' => $purchaseReturn],
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin-left' => 1,
                '',
                '',
                0,
                '',
                1,
                1,
                1,
                1,
                1,
                1,
                'L',
            ]
        );
        $name = \Carbon\Carbon::now()->format('d-m-Y');

        return $pdf->stream($name . '.pdf');
    }
}
