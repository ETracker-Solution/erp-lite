<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\AccountTransaction;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\InventoryTransaction;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\PreOrder;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Yajra\DataTables\Facades\DataTables;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Sale::query()->select([
                'id',
                'invoice_number',
                'subtotal',
                'discount',
                'grand_total',
                'status',
                'created_at',
                'date',
                'membership_discount_amount',
                'special_discount_amount',
                'couponCodeDiscountAmount',
            ]);

            if (auth()->user()?->employee?->outlet_id) {
                $query->where('outlet_id', auth()->user()->employee->outlet_id)
                    ->where('date', date('Y-m-d'));
            } elseif (!filled(request()->input('search.value'))) {
                $query->where('date', '>=', now()->subMonths(6)->toDateString());
            }

            return DataTables::eloquent($query->latest('id'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('sale.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', fn ($row) => showStatus($row->status))
                ->editColumn('subtotal', fn ($row) => number_format((float) $row->subtotal, 2))
                ->editColumn('grand_total', fn ($row) => number_format((float) $row->grand_total, 2))
                ->editColumn('discount', function ($row) {
                    $total = (float) $row->discount
                        + (float) $row->membership_discount_amount
                        + (float) $row->special_discount_amount
                        + (float) $row->couponCodeDiscountAmount;
                    return number_format($total, 2);
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }

        return view('sale.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user_store = null;
        $outlet_id = null;
        if (!auth()->user()->is_super && auth()->user()?->employee?->outlet_id) {
            $user_store = Store::query()
                ->where([
                    'doc_type' => 'outlet',
                    'doc_id' => auth()->user()->employee->outlet_id,
                    'status' => 'active',
                ])
                ->first(['id', 'name', 'doc_id', 'doc_type']);
            $outlet_id = $user_store?->doc_id;
        }

        return view('sale.create2', [
            'groups' => ChartOfInventory::query()
                ->where(['type' => 'group', 'rootAccountType' => 'FG', 'status' => 'active'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'stores' => Store::query()
                ->where(['type' => 'FG', 'doc_type' => 'outlet', 'status' => 'active'])
                ->orderBy('name')
                ->get(['id', 'name']),
            'user_store' => $user_store,
            'delivery_points' => Outlet::query()->select('id', 'name')->orderBy('name')->get(),
            'user_outlet_id' => $outlet_id,
            'payment_methods' => salePaymentMethodOptions(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        $request->validate([
            'products' => 'array',
            'description' => 'nullable',
        ]);

        // dd( $request->all());
        try {
            DB::beginTransaction();

            $selectedDate = Carbon::parse($request->date)->format('Y-m-d');
            $deliveryDate = Carbon::parse($request->delivery_date)->format('Y-m-d');
            $delivery_charge = $request->delivery_charge ?? 0;
            $additional_charge = $request->additional_charge ?? 0;
            $delivery_time = $request->delivery_time ?? null;
            $customer_id = 1;
            if ($request->customer_number) {
                $customer = Customer::where('mobile', $request->customer_number)->first();
                if (!$customer) {
                    $customer = Customer::create([
                        'name' => 'New Customer',
                        'mobile' => $request->customer_number
                    ]);
                }
                $customer_id = $customer->id;
            }
            if ($request->store_id) {
                $store = Store::find($request->store_id);
            } else {
                $store = Store::where(['doc_type' => 'outlet', 'doc_id' => \auth()->user()->employee->outlet_id])->first();
            }
            $outlet = Outlet::find($store->doc_id);
            $outlet_id = $outlet->id;
            $request->merge(['delivery_point_id' => $request->delivery_point_id ?? $outlet_id]);

            $sale = new Sale();
            $sale->invoice_number = generateUniqueUUID($outlet_id, Sale::class, 'invoice_number');
            $sale->subtotal = 0;
            $sale->discount = $request->discount ?? 0;
            $sale->grand_total = 0;
            $sale->receive_amount = $request->receive_amount ?? 0;
            $sale->change_amount = $request->change_amount ?? 0;
            $sale->customer_id = $customer_id;
            $sale->date = $selectedDate;
            $sale->delivery_time = $delivery_time;
            $sale->delivery_charge = $delivery_charge;
            $sale->additional_charge = $additional_charge;
            $sale->created_by = Auth::id();
            $sale->outlet_id = $outlet_id;
//            New Columns
            $sale->membership_discount_percentage = $request->membership_discount_percentage;
            $sale->membership_discount_amount = $request->membership_discount_amount;
            $sale->special_discount_value = $request->special_discount_value;
            $sale->special_discount_amount = $request->special_discount_amount;
            $sale->couponCode = $request->couponCode;
            $sale->couponCodeDiscountType = $request->couponCodeDiscountType;
            $sale->couponCodeDiscountValue = $request->couponCodeDiscountValue;
            $sale->couponCodeDiscountAmount = $request->couponCodeDiscountAmount;
            $sale->total_discount_type = $request->total_discount_type;
            $sale->total_discount_value = $request->total_discount_value;
            $sale->total_discount_amount = $request->total_discount_amount;
            $sale->save();

            $products = $request->get('products');
            $stockProductIds = collect($products)
                ->filter(fn ($row) => ($row['is_readonly'] ?? 'true') == 'true')
                ->pluck('item_id')
                ->filter()
                ->unique()
                ->values();
            $stockByProduct = $stockProductIds->isEmpty()
                ? collect()
                : getInventoryQuantities($stockProductIds, $store->id, true);
            $rateByProduct = averageFGRates(
                collect($products)->pluck('item_id')->filter()->unique()->values()
            );

            $computedSubtotal = 0;
            $computedLineDiscount = 0;
            $avgProductionPrice = 0;

            foreach ($products as $row) {
                if ($row['is_readonly'] == 'false') {
                    $find = ChartOfInventory::find($row['item_id']);
                    $newProduct = ChartOfInventory::create([
                        'name' => $row['item_name'] . '-' . date('ymdhis'),
                        'type' => 'item',
                        'parent_id' => $find->parent_id,
                        'rootAccountType' => 'FG',
                        'status' => 'active',
                    ]);
                    $row['item_id'] = $newProduct->id;
                }
                $row['product_id'] = $row['item_id'];
                $row['unit_price'] = $row['rate'];
                $currentStock = $stockByProduct[$row['product_id']] ?? 0;
                if (($currentStock < $row['quantity']) && $row['is_readonly'] == 'true' && ($request->sales_type != 'pre_order' && $outlet_id == $request->delivery_point_id)) {
                    DB::rollBack();
                    Toastr::error('Quantity cannot more then ' . $currentStock . ' !', '', ["progressBar" => true]);
                    return back();
                }
                if ($row['is_readonly'] == 'true') {
                    $stockByProduct[$row['product_id']] = $currentStock - $row['quantity'];
                }

                $discount_type = isset($row['discountType']) ? $row['discountType'] : null;
                $discount_value = isset($row['product_discount']) ? $row['product_discount'] : 0;

                $row['discount_type'] = $discount_type;
                $row['discount_value'] = $discount_value;
                $amount = $row['unit_price'] * $row['quantity'];
                $discount = lineDiscountAmount($amount, $discount_type, $discount_value);
                $row['discount'] = $discount;
                $computedSubtotal += $amount;
                $computedLineDiscount += $discount;

                $fgRate = $rateByProduct[$row['product_id']] ?? 0;
                $row['cogs'] = $fgRate * $row['quantity'];

                $sale_item = $sale->items()->create($row);
                $sale_item['date'] = date('Y-m-d');
                $sale_item['coi_id'] = $row['product_id'];
                $sale_item['rate'] = $fgRate;
                $sale_item['amount'] = $sale_item['rate'] * $row['quantity'];
                $sale_item['store_id'] = $store->id;
                if ($row['is_readonly'] == 'true' && ($request->sales_type != 'pre_order' && $outlet_id == $request->delivery_point_id)) {
                    addInventoryTransaction(-1, 'POS', $sale_item);
                }
                $avgProductionPrice += $sale_item['amount'];
            }

            $headerDiscounts = ($request->membership_discount_amount ?? 0)
                + ($request->special_discount_amount ?? 0)
                + ($request->couponCodeDiscountAmount ?? 0)
                + ($request->total_discount_amount ?? 0);
            $sale->subtotal = $computedSubtotal;
            $sale->discount = $computedLineDiscount;
            $sale->grand_total = max(0, $computedSubtotal - $computedLineDiscount - $headerDiscounts + $delivery_charge + $additional_charge);
            $salesAmount = $sale->grand_total;
            $receive_amount = 0;
            foreach ($request->payment_methods as $paymentMethod) {
                $receive_amount += $paymentMethod['amount'];
            }
            $sale->receive_amount = $receive_amount;
            $sale->change_amount = $receive_amount - $sale->grand_total;
            $sale->save();
            foreach ($request->payment_methods as $paymentMethod) {
                $payment = Payment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $customer_id ?? null,
                    'payment_method' => $paymentMethod['method'],
                    'amount' => ($paymentMethod['method'] == 'cash' && $sale->change_amount > 0) ? ($paymentMethod['amount'] - $sale->change_amount) : $paymentMethod['amount'] ?? 0,
                ]);
                $sale->amount = $payment->amount;
                $prevSale = $sale;
                if ($paymentMethod['method'] == 'exchange') {
                    $ret = SalesReturn::where('uid', $request->returnNumber)->first();
                    Sale::find($prevSale['id'])->update([
                        'exchange_id' => $ret->id
                    ]);
                }
                postSalePaymentTransaction($sale, $paymentMethod['method'], $outlet_id, $customer_id);
                unset($sale->amount);
            }

            //Start Loyalty Effect
            pointEarnAndUpgradeMember($sale->id, $customer_id ?? null, $salesAmount);
            //End Loyalty Effect
            $sale->amount = $salesAmount;
            addAccountsTransaction('POS', $sale, getAccountsReceiveableGLId(), getIncomeFromSalesGLId());

            $sale->amount = $avgProductionPrice;
            addAccountsTransaction('POS', $sale, getCOGSGLId(), getFGInventoryGLId());

            if ($customer_id != 1 && $salesAmount > $receive_amount) {
                $sale->amount = $salesAmount - $receive_amount;
                addCustomerTransaction($sale);
            }

            if ($request->sales_type == 'pre_order') {
                $this->preOrderfromSales($sale, $deliveryDate, $delivery_charge, $delivery_time, $request->description, $request->size, $request->flavour, $request->cake_message, $request->attachments, $request->delivery_point_id);
            }
            if (($receive_amount < $sale->grand_total) || $outlet_id != $request->delivery_point_id || $request->sales_type == 'pre_order') {
                $this->othersOutletDelivery($sale, $request->delivery_point_id);
            }

            if ($request->couponCode) {
                $promoCode = \App\Models\PromoCode::where('code', $request->couponCode)->first();

                if ($promoCode) {
                    $customerPromo = \App\Models\CustomerPromoCode::firstOrNew([
                        'customer_id' => $sale->customer_id,
                        'promo_code_id' => $promoCode->id,
                    ]);
                    $customerPromo->already_used = ($customerPromo->already_used ?? 0) + 1;
                    // Mark as used
                    $customerPromo->used = 1;
                    $customerPromo->save();
                }
            }

            DB::commit();

            Toastr::success('Sale Order Successful!.', '', ["progressBar" => true]);
            return redirect()->route('sales.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressbar" => true]);
            return back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sale = Sale::query()
            ->with([
                'outlet:id,name,address',
                'customer:id,name,mobile,email,address',
                'items.coi:id,name,unit_id',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        return view('sale.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        try {
            DB::beginTransaction();
            $customer = $sale->customer;
            $membership = $customer?->membership;
            $membershipPointHistory = $customer
                ? $customer->membershipPointHistories()->where('sale_id', $sale->id)->first()
                : null;
            AccountTransaction::where([
                'doc_type' => 'POS',
                'doc_id' => $sale->id,
            ])->delete();
            if ($membership && $membershipPointHistory) {
                $membership->decrement('point', $membershipPointHistory->point);
                $membershipPointHistory->delete();
            }
            $preOrder = $sale->preOrder;
            if ($preOrder) {
                $preOrder->update([
                    'status'=>'cancelled'
                ]);
            }

            $delivery_of_pre_order = OthersOutletSale::where('invoice_number', $sale->invoice_number)
                ->where('outlet_id', $sale->outlet_id)
                ->orderByDesc('id')
                ->first();
            if ($delivery_of_pre_order) {
                $delivery_of_pre_order->delete();
            }

            InventoryTransaction::where('doc_type','POS')->whereIn('doc_id',$sale->items()->pluck('id')->toArray())->delete();

            $sale->update([
                'status'=>'cancelled'
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressbar" => true]);
            return back();
        }
        Toastr::success('Successfully Cancelled!.', '', ["progressbar" => true]);
        return redirect()->route('sales.index');
    }

    public function fetch_product_sale($id)
    {

        $product = Product::findOrFail($id);
        $data = [
            'product_name' => $product->name,
            'sale_price' => $product->selling_price,
            'buy_price' => $product->buying_price,
            'product_id' => $id,
            'stock' => \App\Classes\AvailableProductCalculation::product_id($id),
        ];
        return $data;
    }

    public function pdfDownload($id)
    {
        $sale = Sale::query()
            ->with([
                'outlet:id,name,address',
                'customer:id,name,mobile,email,address',
                'items.coi:id,name,unit_id',
                'items.coi.unit:id,name',
            ])
            ->findOrFail(decrypt($id));

        $pdf = Pdf::loadView(
            'sale.pdf',
            ['sale' => $sale],
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin_left' => 8,
                'margin_right' => 8,
                'margin_top' => 8,
                'margin_bottom' => 8,
            ]
        );

        return $pdf->stream('INV-' . ($sale->invoice_number ?: $sale->id) . '.pdf');
    }

    public function getInvoiceByOutlet(Request $request, $store_id)
    {
        $store = Store::find($store_id);
        // Do not mint here — store() assigns the real invoice on save.
        $store->invoice = 'Assigned on save';
        $store->outlet = $store->doc_type == 'outlet' ? $store->doc_id : null;
        return $store;
    }

    protected function preOrderfromSales($sale, $delivery_date, $delivery_charge, $delivery_time, $description, $size, $flavour, $cake_message, $images, $delivery_point_id)
    {
        $data = [
            'customer_id' => $sale->customer_id,
            'outlet_id' => $sale->outlet_id,
            'order_date' => $sale->date,
            'delivery_date' => $delivery_date,
            'delivery_charge' => $delivery_charge,
            'delivery_time' => $delivery_time,
            'subtotal' => $sale->subtotal,
            'discount' => $sale->discount,
            'vat' => $sale->vat,
            'grand_total' => $sale->grand_total,
            'advance_amount' => $sale->receive_amount,
            'remark' => $description,
            'flavour' => $flavour,
            'cake_message' => $cake_message,
            'size' => $size,
            'created_by' => auth()->user()->id,
            'order_number' => $sale->invoice_number,
            'delivery_point_id' => $delivery_point_id,
            'sale_id' => $sale->id
        ];
        $preOrder = PreOrder::create($data);

        $products = $sale->items;
        foreach ($products as $product) {
            $product->coi_id = $product->product_id;
            $preOrder->items()->create($product->toArray());
        }
        if (isset($images)) {
            foreach ($images as $image) {
                $filename = date('Ymdmhs').uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('/upload'), $filename);
                $preOrder->attachments()->create([
                    'image' => $filename
                ]);
            }
        }


    }

    protected function othersOutletDelivery($oldSale, $delivery_point_id)
    {
        $sale = new OthersOutletSale();
        $sale->invoice_number = $oldSale->invoice_number;
        $sale->subtotal = $oldSale->subtotal;
        $sale->discount = $oldSale->discount ?? 0;
        $sale->grand_total = $oldSale->grand_total;
        $sale->receive_amount = $oldSale->receive_amount ?? 0;
        $sale->change_amount = $oldSale->change_amount ?? 0;
        $sale->customer_id = $oldSale->customer_id;
        $sale->status = 'pending';
        $sale->date = $oldSale->date;
        $sale->delivery_time = $oldSale->delivery_time;
        $sale->created_by = $oldSale->created_by;
        $sale->outlet_id = $oldSale->outlet_id;
        $sale->delivery_point_id = $delivery_point_id;
        $sale->delivery_charge = $oldSale->delivery_charge;
        $sale->additional_charge = $oldSale->additional_charge;
        $sale->save();

        $products = $oldSale->items;
        foreach ($products as $product) {
            $sale->items()->create($product->toArray());
        }
        $sale->save();
    }
}
