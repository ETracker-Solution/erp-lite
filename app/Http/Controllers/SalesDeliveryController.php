<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InventoryTransaction;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\Printer;
use Yajra\DataTables\Facades\DataTables;

class SalesDeliveryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = $this->getFilteredData();

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('sales_delivery.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('grand_total', fn ($row) => number_format((float) $row->grand_total, 2))
                ->addColumn('due', function ($row) {
                    return number_format(
                        max((float) $row->grand_total - ((float) $row->receive_amount + (float) $row->delivery_point_receive_amount), 0),
                        2
                    );
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }

        return view('sales_delivery.index');
    }

    public function create()
    {
        $user_store = null;
        $outletId = auth()->user()?->employee?->outlet_id;
        $prefillSaleId = request('sale_id') ? (string) request('sale_id') : '';
        $prefillStoreId = '';
        $prefillSale = null;

        if (!auth()->user()->is_super && $outletId) {
            $user_store = Store::query()
                ->where([
                    'doc_type' => 'outlet',
                    'doc_id' => $outletId,
                    'status' => 'active',
                ])
                ->first(['id', 'name', 'doc_id']);
        }

        if ($prefillSaleId !== '') {
            $prefillSale = OthersOutletSale::query()
                ->select(['id', 'invoice_number', 'date', 'delivery_point_id', 'status'])
                ->where('id', $prefillSaleId)
                ->where('status', '!=', 'delivered')
                ->first();

            if ($prefillSale) {
                $deliveryStore = Store::query()
                    ->where([
                        'type' => 'FG',
                        'doc_type' => 'outlet',
                        'doc_id' => $prefillSale->delivery_point_id,
                        'status' => 'active',
                    ])
                    ->first(['id', 'name', 'doc_id']);

                if ($deliveryStore) {
                    $prefillStoreId = (string) $deliveryStore->id;
                    if (!$user_store) {
                        $user_store = $deliveryStore;
                    }
                }
            } else {
                $prefillSaleId = '';
            }
        }

        return view('sales_delivery.create', [
            'stores' => Store::query()
                ->select('id', 'name')
                ->where(['type' => 'FG', 'doc_type' => 'outlet', 'status' => 'active'])
                ->orderBy('name')
                ->get(),
            'delivery_points' => Outlet::query()->select('id', 'name')->orderBy('name')->get(),
            'user_store' => $user_store,
            'payment_methods' => salePaymentMethodOptions(),
            'prefill_sale_id' => $prefillSaleId,
            'prefill_store_id' => $prefillStoreId ?: ($user_store->id ?? ''),
            'prefill_sale' => $prefillSale,
        ]);
    }

    public function searchSales(Request $request)
    {
        $query = OthersOutletSale::query()
            ->select('id', 'invoice_number', 'grand_total', 'receive_amount', 'delivery_point_id', 'outlet_id', 'date')
            ->where('status', '!=', 'delivered');

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('store_id')) {
            $outlet_id = Store::find($request->store_id)?->doc_id;
            if ($outlet_id) {
                $query->where('delivery_point_id', $outlet_id);
            }
        }

        if (auth()->user()?->employee?->outlet_id && !auth()->user()->is_super) {
            $query->where('delivery_point_id', auth()->user()->employee->outlet_id);
        }

        return response()->json($query->latest('id')->limit(50)->get());
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $originalSale = OthersOutletSale::findOrFail($request->sale_id);

            if ($originalSale->status === 'delivered') {
                DB::rollBack();
                Toastr::error('This sale has already been processed!', '', ["progressBar" => true]);
                return back();
            }

            $originalSaleItems = $originalSale->items;
            $store = Store::findOrFail($request->store_id);
            $main_outlet = $originalSale->outlet;
            $delivery_outlet = Outlet::findOrFail($originalSale->delivery_point_id);
            $delivery_store_id = $delivery_outlet->stores()->where('status', 'active')->first()->id;

            $originalSale->update(['status' => 'processing']);

            $outlet = Outlet::findOrFail($store->doc_id);
            $outlet_id = $outlet->id;

            $sale = Sale::where('invoice_number', $originalSale->invoice_number)
                ->where('outlet_id', $originalSale->outlet_id)
                ->orderByDesc('id')
                ->first();

            if (!$sale) {
                DB::rollBack();
                Toastr::error('Original sale not found for this delivery!', '', ["progressBar" => true]);
                return back();
            }

            $sale->date = date('Y-m-d');
            $salesAmount = $sale->grand_total;
            $customer_id = $sale->customer_id ?? $originalSale->customer_id;

            $shouldDeductStock = $sale->preOrder
                || ((int) $originalSale->outlet_id !== (int) $originalSale->delivery_point_id);

            if ($shouldDeductStock) {
                $productIds = collect($originalSaleItems)->pluck('product_id')->filter()->unique()->values();
                $stockByProduct = $productIds->isEmpty()
                    ? collect()
                    : getInventoryQuantities($productIds, $delivery_store_id, true);
                $rateByProduct = averageFGRates($productIds);

                foreach ($originalSaleItems as $row) {
                    $row = collect($row)->toArray();
                    $currentStock = $stockByProduct[$row['product_id']] ?? 0;
                    if ($currentStock < $row['quantity']) {
                        DB::rollBack();
                        Toastr::error('Quantity cannot more then ' . $currentStock . ' !', '', ["progressBar" => true]);
                        return back();
                    }
                    $stockByProduct[$row['product_id']] = $currentStock - $row['quantity'];

                    $sale_item = $sale->items()->where('product_id', $row['product_id'])->first();
                    $sale_item['date'] = date('Y-m-d');
                    $sale_item['coi_id'] = $row['product_id'];
                    $sale_item['rate'] = $rateByProduct[$row['product_id']] ?? 0;
                    $sale_item['amount'] = $sale_item['rate'] * $row['quantity'];
                    $sale_item['store_id'] = $delivery_store_id;
                    addInventoryTransaction(-1, 'POS', $sale_item);
                }
            }

            $delivery_receive = 0;
            $receivable_amount = $salesAmount - $sale->receive_amount;

            foreach ($request->payment_methods as $paymentMethod) {
                $delivery_receive += $paymentMethod['amount'];
            }
            $change_amount = $delivery_receive - $receivable_amount;

            foreach ($request->payment_methods as $paymentMethod) {
                $payment = Payment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $customer_id,
                    'payment_method' => $paymentMethod['method'],
                    'amount' => ($paymentMethod['method'] == 'cash' && $change_amount > 0)
                        ? ($paymentMethod['amount'] - $change_amount)
                        : $paymentMethod['amount'],
                ]);
                $sale->amount = $payment->amount;
                postSalePaymentTransaction($sale, $paymentMethod['method'], $outlet_id, $customer_id);
                unset($sale->amount);
            }

            $originalSale->update([
                'status' => 'delivered',
                'payment_status' => 'paid',
                'delivery_point_receive_amount' => $delivery_receive,
            ]);

            if ($delivery_receive > 0) {
                $sale->amount = $delivery_receive;
                addCustomerTransaction($sale, -1);
            }

            DB::commit();
            Toastr::success('Sale Delivered Successful!.', '', ["progressBar" => true]);
            return redirect()->route('sales-deliveries.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
    }

    public function show(string $id)
    {
        $sale = OthersOutletSale::query()
            ->with([
                'deliveryPoint:id,name',
                'outlet:id,name',
                'customer:id,name,mobile',
                'items.coi:id,name,parent_id,unit_id',
                'items.coi.parent:id,name',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        return view('sales_delivery.show', compact('sale'));
    }

    public function getItemData($id)
    {
        return OthersOutletSale::with('deliveryPoint', 'outlet', 'items.coi', 'customer')->findOrFail($id);
    }

    public function print()
    {
        $printer = null;
        try {
            $connector = new CupsPrintConnector($this->getDefaultPrinter());
            $printer = new Printer($connector);
            $printer->text("Invoice #" . 1 . "\n");
            $printer->text("Date: " . '2024-01-01' . "\n");
            $printer->text("Customer: " . 'Noman' . "\n");
            $printer->feed();
            $printer->cut();
        } catch (\Exception $e) {
            \Log::error('Printing failed: ' . $e->getMessage());
            return "Failed to print: " . $e->getMessage();
        } finally {
            if ($printer) {
                try {
                    $printer->close();
                } catch (\Exception $e) {
                    \Log::error('Failed to close printer: ' . $e->getMessage());
                }
            }
        }
    }

    public function getDefaultPrinter()
    {
        $output = [];
        exec("lpstat -d", $output);
        return count($output) > 0 ? trim(str_replace("system default destination:", "", $output[0])) : null;
    }

    private function getFilteredData()
    {
        $query = OthersOutletSale::query()
            ->select([
                'others_outlet_sales.id',
                'others_outlet_sales.invoice_number',
                'others_outlet_sales.date',
                'others_outlet_sales.status',
                'others_outlet_sales.grand_total',
                'others_outlet_sales.receive_amount',
                'others_outlet_sales.delivery_point_receive_amount',
                'others_outlet_sales.outlet_id',
                'others_outlet_sales.delivery_point_id',
                'others_outlet_sales.created_at',
            ])
            ->with(['deliveryPoint:id,name', 'outlet:id,name']);

        if (auth()->user()?->employee?->outlet_id) {
            $query->where('delivery_point_id', auth()->user()->employee->outlet_id);
        } elseif (!(auth()->user()->is_super || (auth()->user()?->employee?->user_of == 'ho'))) {
            $query->whereRaw('1 = 0');
        }

        if (request()->filled('status')) {
            $query->where('status', request()->status);
        }

        if (request()->filled('from_date') && request()->filled('to_date')) {
            $from_date = Carbon::parse(request()->from_date)->format('Y-m-d');
            $to_date = Carbon::parse(request()->to_date)->format('Y-m-d');
            $query->whereBetween('date', [$from_date, $to_date]);
        }

        return $query->latest('id');
    }
}
