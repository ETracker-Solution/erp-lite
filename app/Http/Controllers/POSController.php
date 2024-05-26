<?php

namespace App\Http\Controllers;

use App\Classes\InvoiceNumber;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\ChartOfInventory;
use App\Models\CustomerPromoCode;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\PromoCode;
use App\Models\Store;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\SubCategory;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class POSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!\auth()->user()->employee->outlet_id) {
            Toastr::error('You are not permitted to do this');
            return back();
        }
        return view('pos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $selectedDate = date('Y-m-d');
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
            $outlet_id = \auth('web')->user()->outlet_id;
            $outlet = Outlet::find($outlet_id);
            $store = Store::find(['doc_type' => 'outlet', 'doc_id' => $outlet->id]);
            $sale = new Sale();
            $sale->invoice_number = $request->invoice_number ?? InvoiceNumber::generateInvoiceNumber($outlet_id, $selectedDate);

            $sale->date = date('Y-m-d');
            $sale->subtotal = $request->sub_total;
            $sale->discount = $request->discount;
            $sale->grand_total = $request->grand_total;
            $sale->customer_id = $customer_id;
            $sale->created_by = auth('web')->user()->id;

            $sale->payment_status = 'paid';
            $sale->outlet_id = $outlet_id;
            $sale->save();
            $products = $request->get('products');

            $salesAmount = $sale->grand_total;
            $avgProductionPrice = 0;
//            return $products;
            foreach ($products as $row) {
                $row['product_id'] = $row['id'];
                $row['unit_price'] = $row['price'];
                $currentStock = availableInventoryBalance($row['product_id'], $store->id);
                if ($currentStock < $row['quantity']) {
                    Toastr::error('Delivery Quantity cannot more then ' . $currentStock . ' !', '', ["progressBar" => true]);
                    return back();
                }


                $sale_item = $sale->items()->create($row);
                $sale_item['date'] = date('Y-m-d');
                $sale_item['coi_id'] = $row['id'];
                $sale_item['rate'] = averageFGRate($row['id']);
                $sale_item['amount'] = $sale_item['rate'] * $row['quantity'];
                $sale_item['store_id'] = $store;
                addInventoryTransaction(-1, 'POS', $sale_item);

                $avgProductionPrice += $sale_item['amount'];
            }
            $receive_amount = 0;
            foreach ($request->payment_methods as $paymentMethod) {
                $receive_amount += $paymentMethod['amount'];
                Payment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $customer_id ?? null,
                    'payment_method' => $paymentMethod['method'],
                    'amount' => $paymentMethod['amount'],
                ]);
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

            $sale->receive_amount = $receive_amount;
            $sale->change_amount = $receive_amount - $sale->grand_total;
            $sale->save();
            //Start Loyalty Effect
            pointEarnAndUpgradeMember($sale->id, $customer_id ?? null, $request->grand_total);
            //End Loyalty Effect
            $sale->amount = $salesAmount;
            addAccountsTransaction('POS', $sale, getAccountsReceiveableGLId(), getIncomeFromSalesGLId());
            $sale->amount = $avgProductionPrice;
            addAccountsTransaction('POS', $sale, getCOGSGLId(), getFGInventoryGLId());
//            $sale->amount = $salesAmount;
//            addAccountsTransaction('POS',$sale, getCashGLID(), getAccountsReceiveableGLId());


            DB::commit();

        } catch (\Exception $error) {
            DB::rollBack();
            Log::emergency("File:" . $error->getFile() . "Line:" . $error->getLine() . "Message:" . $error->getMessage());
//            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
//            return response()->json(['message' => 'error'], 500);
            return response()->json(['message' => $error->getMessage()], 500);
        }
        return response()->json(['message' => 'success', 'sale' => $sale]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAllProducts(Request $request)
    {
        $products = ChartOfInventory::where(['type' => 'item', 'status' => 'active', 'rootAccountType' => 'FG']);
        if ($request->category && $request->category != null) {
            $products->where('parent_id', $request->category);
        }
        if ($request->search_term && $request->search_term != null) {
            $products->where('name', 'like', '%' . $request->search_term . '%');
        }
        $products = $products->get();

        foreach ($products as $product) {
            $product->stock = availableInventoryBalance($product->id, 4);
        }
        return $products;
    }

    public function getAllProductCategories(Request $request)
    {
        return ChartOfInventory::where(['rootAccountType' => 'FG', 'status' => 'active'])->whereHas('subChartOfInventories', function ($q) {
            return $q->where('type', 'item');
        })->get();
    }

    public function getProductByNameSkuBarCode(Request $request)
    {
        $searchString = $request->search_term;
        $product = Product::where(['type' => 'finish', 'status' => 'active'])->where(function ($q) use ($searchString) {
            $q->where('name', 'like', '%' . $searchString . '%')->orWhere('sku', 'like', '%' . $searchString . '%');
        });
        if ($product->count() == 1) {
            return $product->first();
        }
        return 'multiple';
    }

    public function getView()
    {
        $view_for = \request()->view_for;
        $view = view('pos.pos-page');
        if ($view_for == 'home') {
            $view = view('pos.pos-page');
        } else if ($view_for == 'customers') {
            $view = view('pos.customer-page');
        }
        return $view;
    }

    public function getAllCustomers(Request $request)
    {
        $data = Customer::where(['status' => 'active', 'type' => 'regular']);
        return $data->get();
    }

    public function getCustomerByNumber(Request $request)
    {
        $data = Customer::where('mobile', $request->number)->first();
        if ($data) {
            $data->current_point = $data->membership ? $data->membership->point : 0;
        }
        return $data;
    }

    public function getAllOrders()
    {
        return Sale::with('items.product', 'customer')->withSum('items', 'quantity')->latest()->get();
    }

    public function getCouponDiscountValue(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        $user = Customer::where('mobile', $request->user)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Customer Number Required']);
        }
        $code = PromoCode::where('code', $request->code)->where('start_date', '<=', $now)->where('end_date', '>=', $now)->first();
        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Invalid Code']);
        }
        $alreadyUsed = CustomerPromoCode::where(['customer_id' => $user->id, 'promo_code_id' => $code->id])->first();
        if ($alreadyUsed && $alreadyUsed->used > 0) {
            return response()->json(['success' => false, 'message' => 'Code Already Used ']);
        }

        return response()->json(['success' => true, 'message' => 'Code Found ', 'data' => $code]);
    }

    public function addCustomer(StoreCustomerRequest $request)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Customer::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something Went Wrong']);
        }
        return response()->json(['success' => true, 'message' => 'Customer Added']);
    }

    public function updateCustomer(UpdateCustomerRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Customer::findOrFail(($id))->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            return $error;
            return response()->json(['success' => false, 'message' => 'Something Went Wrong']);
        }
        return response()->json(['success' => true, 'message' => 'Customer Updated']);
    }

    public function printInvoice($id)
    {
        $sale = Sale::find($id);
//        return view('pos.print.order', compact('sale'));
        $options = new Options();
        $options = ['chroot' => base_path()];
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('pos.print.order', compact('sale'))->render());
        // (Optional) Set paper size and orientation
//        $dompdf->setPaper('A4', $this->pageOrientation);
        // Render the HTML as PDF
        $dompdf->render();
        return $dompdf->stream('order', ["Attachment" => false]);
    }
}
