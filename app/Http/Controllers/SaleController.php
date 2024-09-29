<?php

namespace App\Http\Controllers;

use App\Classes\InvoiceNumber;
use App\Classes\PreOrderNumber;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\ChartOfInventory;
use App\Models\Customer;
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
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $data = Sale::where(['outlet_id' => \auth()->user()->employee->outlet_id])->latest();
        } else {
            $data = Sale::latest();
        }
        if (\request()->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('sale.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
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
        $serial_no = null;
        $user_store = null;
        $outlet_id = null;
        if (!auth()->user()->is_super) {
            if(\auth()->user()->employee->outlet_id){
                $user_store = Store::where(['doc_type' => 'outlet', 'doc_id' => \auth()->user()->employee->outlet_id])->first();
                $outlet_id = $user_store->doc_id;
                $serial_no = generateUniqueUUID($outlet_id, Sale::class, 'invoice_number');
            }else{

                Toastr::success('You are not allowed to create sales!.', '', ["progressBar" => true]);
                return redirect()->route('sales.index');
            }
            // $serial_no = InvoiceNumber::generateInvoiceNumber(\auth()->user()->employee->outlet_id);
        }
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => Store::where(['type' => 'FG', 'doc_type' => 'outlet'])->get(),
            'serial_no' => $serial_no,
            'customers' => Customer::where('status', 'active')->get(),
            'user_store' => $user_store,
            'invoice_number' => $serial_no,
            'delivery_points' => Outlet::all(),
            'user_outlet_id' => $outlet_id

        ];
//        return $data;
        return view('sale.create2', $data);
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
            // $sale->invoice_number = $request->invoice_number ?? InvoiceNumber::generateInvoiceNumber($outlet_id, $selectedDate);
            $sale->subtotal = $request->subtotal;
            $sale->discount = $request->discount ?? 0;
            $sale->grand_total = $request->grandtotal;
            $sale->receive_amount = $request->receive_amount ?? 0;
            $sale->change_amount = $request->change_amount ?? 0;
            $sale->customer_id = $customer_id;
            $sale->date = $selectedDate;
            $sale->delivery_time = $delivery_time;
//            $sale->description = $request->description;
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

            $salesAmount = $sale->grand_total;
            $avgProductionPrice = 0;

            foreach ($products as $row) {
                if ($row['is_readonly'] == 'false') {
                    $newProduct = ChartOfInventory::create([
                        'name' => $row['item_name'] . '-' . date('ymdhis'),
                        'type' => 'item',
                        'parent_id' => 174,
                        'rootAccountType' => 'FG',
                        'status' => 'active',
                    ]);
                    $row['item_id'] = $newProduct->id;
                }
                $row['product_id'] = $row['item_id'];
                $row['unit_price'] = $row['rate'];
                $currentStock = availableInventoryBalance($row['product_id'], $store->id);
                if (($currentStock < $row['quantity']) && $row['is_readonly'] == 'true' && ($request->sales_type != 'pre_order' && $outlet_id == $request->delivery_point_id)) {
                    Toastr::error('Quantity cannot more then ' . $currentStock . ' !', '', ["progressBar" => true]);
                    return back();
                }

                $discount_type = isset($row['discountType']) ? $row['discountType'] : null;
                $discount_value = $row['product_discount'];

                $row['discount_type'] = $discount_type;
                $row['discount_value'] = $discount_value;
                $amount = $row['unit_price'] * $row['quantity'];
                if ($discount_type == 'p') {
                    $discount = ($amount * $discount_value) / 100;
                } elseif ($discount_type == 'f') {
                    $discount = ($amount - $discount_value);
                } else {
                    $discount = 0;
                }
                $row['discount'] = $discount;

                $sale_item = $sale->items()->create($row);
                $sale_item['date'] = date('Y-m-d');
                $sale_item['coi_id'] = $row['product_id'];
                $sale_item['rate'] = averageFGRate($row['product_id']);
                $sale_item['amount'] = $sale_item['rate'] * $row['quantity'];
                $sale_item['store_id'] = $store->id;
                if ($row['is_readonly'] == 'true' && $request->sales_type != 'pre_order') {
                    addInventoryTransaction(-1, 'POS', $sale_item);
                }
                $avgProductionPrice += $sale_item['amount'];
            }
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
                    'amount' => ($paymentMethod['method'] == 'cash' && $sale->change_amount > 0) ? ($paymentMethod['amount'] - $sale->change_amount) : $paymentMethod['amount'],
                ]);
                $sale->amount = $payment->amount;
                $prevSale = $sale;
                if ($paymentMethod['method'] == 'exchange') {
                    $ret = SalesReturn::where('uid', $request->returnNumber)->first();
                    Sale::find($prevSale['id'])->update([
                        'exchange_id'=>$ret->id
                    ]);
                }
                if ($paymentMethod['method'] == 'upay') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'upay'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'rocket') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'rocket'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'bank') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'bank'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'nagad') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'nagad'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'bkash') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'bkash'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'cash') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id,), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'point') {
                    redeemPoint($sale->id, $customer_id, $paymentMethod['amount']);
                    addAccountsTransaction('POS', $sale, getRewardGLID(), getAccountsReceiveableGLId());
                }
                unset($sale->amount);
            }

            //Start Loyalty Effect
            pointEarnAndUpgradeMember($sale->id, $customer_id ?? null, $request->grandtotal);
            //End Loyalty Effect
            $sale->amount = $salesAmount;
            addAccountsTransaction('POS', $sale, getAccountsReceiveableGLId(), getIncomeFromSalesGLId());

            $sale->amount = 0;
            addAccountsTransaction('POS', $sale, getCOGSGLId(), getFGInventoryGLId());

            if ($customer_id != 1 && $salesAmount > $receive_amount) {
                $sale->amount = $salesAmount - $receive_amount;
                addCustomerTransaction($sale);
            }

            if ($request->sales_type == 'pre_order') {
                $this->preOrderfromSales($sale, $deliveryDate,$delivery_charge, $delivery_time, $request->description,$request->size,$request->flavour,$request->cake_message, $request->attachments, $request->delivery_point_id);
            }
            if (($receive_amount < $sale->grand_total) || $outlet_id !== $request->delivery_point_id || $request->sales_type == 'pre_order') {
                $this->othersOutletDelivery($sale, $request->delivery_point_id);
            }
            DB::commit();

            Toastr::success('Sale Order Successful!.', '', ["progressBar" => true]);
            return redirect()->route('sales.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
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
        $sale = Sale::findOrFail(decrypt($id));
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
        //
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
        $data = [
            'sale' => Sale::findOrFail(decrypt($id)),
        ];

        $pdf = PDF::loadView(
            'sale.pdf',
            $data,
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin-left' => 1,

                '', // mode - default ''
                '', // format - A4, for example, default ''
                0, // font size - default 0
                '', // default font family
                1, // margin_left
                1, // margin right
                1, // margin top
                1, // margin bottom
                1, // margin header
                1, // margin footer
                'L', // L - landscape, P - portrait

            ]
        );
        $name = \Carbon\Carbon::now()->format('d-m-Y');

        return $pdf->stream($name . '.pdf');
    }

    public function getInvoiceByOutlet(Request $request, $store_id)
    {
        $store = Store::find($store_id);
        $store->invoice = generateInvoiceCode($store->doc_id, $request->date);
        $store->outlet = $store->doc_type == 'outlet' ? $store->doc_id : null;
        return $store;
    }

    protected function preOrderfromSales($sale, $delivery_date, $delivery_charge, $delivery_time, $description,$size,$flavour,$cake_message, $images, $delivery_point_id)
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
            'sale_id'=>$sale->id
        ];
        $preOrder = PreOrder::create($data);

        $products = $sale->items;
        foreach ($products as $product) {
            $product->coi_id = $product->product_id;
            $preOrder->items()->create($product->toArray());
        }
        if (isset($images))
        {
            foreach ($images as $image) {
                $filename = date('Ymdmhs') . '.' . $image->getClientOriginalExtension();
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
        $sale->save();

        $products = $oldSale->items;
        foreach ($products as $product) {
            $sale->items()->create($product->toArray());
        }
        $sale->save();
    }
}
