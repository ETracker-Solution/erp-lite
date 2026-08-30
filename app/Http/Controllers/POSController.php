<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\ChartOfInventory;
use App\Models\CustomerPromoCode;
use App\Models\Employee;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\PreOrder;
use App\Models\PromoCode;
use App\Models\SalesReturn;
use App\Models\Store;
use App\Models\VerifyOtp;
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
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

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
        $employees = Employee::where(['outlet_id' => \auth()->user()->employee->outlet_id])->get();
        return view('pos.index', compact('employees'));
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
            $outlet_id = \auth('web')->user()->employee->outlet_id;
            $outlet = Outlet::find($outlet_id);
            $store = Store::where(['doc_type' => 'outlet', 'doc_id' => $outlet->id, 'status' => 'active'])->first();
            $sale = new Sale();

            $sale->invoice_number = generateUniqueUUID($outlet_id, Sale::class, 'invoice_number');

            $sale->date = date('Y-m-d');
            $sale->subtotal = 0;
            $sale->discount = $request->discount;
            $sale->grand_total = 0;
            $sale->customer_id = $customer_id;
            $sale->created_by = auth('web')->user()->id;

            $sale->payment_status = 'paid';
            $sale->outlet_id = $outlet_id;
            if ($request->waiter_id) {
                $waiter = Employee::find($request->waiter_id);
                if ($waiter) {
                    $sale->waiter_id = $waiter->id;
                    $sale->waiter_name = $waiter->name;
                }
            }

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
            $productIds = collect($products)->pluck('id')->filter()->unique()->values();
            $stockByProduct = $productIds->isEmpty()
                ? collect()
                : getInventoryQuantities($productIds, $store->id, true);
            $rateByProduct = averageFGRates($productIds);

            $computedSubtotal = 0;
            $computedLineDiscount = 0;
            $avgProductionPrice = 0;
//            return $products;
            foreach ($products as $row) {
                $row['product_id'] = $row['id'];
                $row['unit_price'] = $row['price'];
                $currentStock = $stockByProduct[$row['id']] ?? 0;

                $epsilon = 0.0001;
                if ($currentStock + $epsilon < $row['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Delivery Quantity cannot more then ' . $currentStock . ' !',
                    ], 422);
                }
                $stockByProduct[$row['id']] = $currentStock - $row['quantity'];

                $fgRate = $rateByProduct[$row['id']] ?? 0;
                $row['cogs'] = $fgRate * $row['quantity'];

                $discount_type = $row['discountType'];
                $discount_value = $row['discountValue'];

                $row['discount_type'] = $discount_type;
                $row['discount_value'] = $discount_value;
                $amount = $row['unit_price'] * $row['quantity'];
                $discount = lineDiscountAmount($amount, $discount_type, $discount_value);
                $row['discount'] = $discount;
                $computedSubtotal += $amount;
                $computedLineDiscount += $discount;

                $sale_item = $sale->items()->create($row);
                $sale_item['date'] = date('Y-m-d');
                $sale_item['coi_id'] = $row['id'];
                $sale_item['rate'] = $fgRate;
                $sale_item['amount'] = $sale_item['rate'] * $row['quantity'];
                $sale_item['store_id'] = $store->id;

                addInventoryTransaction(-1, 'POS', $sale_item);

                $avgProductionPrice += $sale_item['amount'];
            }

            $headerDiscounts = ($request->membership_discount_amount ?? 0)
                + ($request->special_discount_amount ?? 0)
                + ($request->couponCodeDiscountAmount ?? 0)
                + ($request->total_discount_amount ?? 0);
            $sale->subtotal = $computedSubtotal;
            $sale->discount = $computedLineDiscount;
            $sale->grand_total = max(0, $computedSubtotal - $computedLineDiscount - $headerDiscounts);
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
                    'amount' => ($paymentMethod['method'] == 'cash' && $sale->change_amount > 0) ? ($paymentMethod['amount'] - $sale->change_amount) : $paymentMethod['amount'],
                ]);
                $sale->amount = $payment->amount;
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
//            if ($request->discount > 0) {
//                $sale->amount = $request->discount;
//                addAccountsTransaction('POS', $sale, getDiscountGLID(), getAccountsReceiveableGLId());
//            }

            if ($request->pre_order_id) {
                PreOrder::find($request->pre_order_id)->update([
                    'sale_id' => $sale->id
                ]);
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
            forgetPosTransactionAbleStockMap($store->id);

        } catch (\Exception $error) {
            DB::rollBack();
            $message = $error->getMessage();
            Log::emergency("File:" . $error->getFile() . "Line:" . $error->getLine() . "Message:" . $error->getMessage());
            if ($error->getCode() == 23000) {
                $message = 'Please Set Account Config';
            }

            return response()->json(['success' => false, 'message' => $message], 500);
        }
        return response()->json(['success' => true, 'message' => 'success', 'sale' => $sale]);
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
        $outlet = auth('web')->user()->employee->outlet;
        $store = Store::where(['doc_type' => 'outlet', 'doc_id' => $outlet->id, 'status' => 'active'])->first();
        $storeId = $store->id;
        $perPage = min((int) $request->get('per_page', 50), 100);
        $page = max((int) $request->get('page', 1), 1);

        $productsQuery = ChartOfInventory::query()
            ->select(['id', 'name', 'price', 'parent_id'])
            ->with(['parent:id,non_discountable'])
            ->where([
                'type' => 'item',
                'status' => 'active',
                'rootAccountType' => 'FG',
            ])
            ->where('price', '>', 0)
            ->when($request->category, function ($query, $category) {
                return $query->where('parent_id', $category);
            })
            ->when($request->filled('search_term'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->search_term . '%');
            })
            ->orderBy('name');

        $paginator = $productsQuery->paginate($perPage, ['*'], 'page', $page);
        $products = $paginator->getCollection();
        $stockMap = getPosTransactionAbleStockMap($storeId);

        $data = $products->map(function ($product) use ($stockMap) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $stockMap[$product->id] ?? 0,
                'discountable' => !($product->parent?->non_discountable ?? false),
            ];
        })->values();

        return response()->json([
            'data' => $data,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'has_more' => $paginator->hasMorePages(),
        ]);
    }

//    public function getAllProducts(Request $request)
//    {
//        $products = ChartOfInventory::where(['type' => 'item', 'status' => 'active', 'rootAccountType' => 'FG']);
//        if ($request->category && $request->category != null) {
//            $products->where('parent_id', $request->category);
//        }
//        if ($request->search_term && $request->search_term != null) {
//            $products->where('name', 'like', '%' . $request->search_term . '%');
//        }
//        $products = $products->get();
//
//        $outlet_id = \auth('web')->user()->employee->outlet_id;
//        $outlet = Outlet::find($outlet_id);
//        $store = Store::where(['doc_type' => 'outlet', 'doc_id' => $outlet->id])->first();
//        foreach ($products as $product) {
//            $product->stock = transactionAbleStock($product, [$store->id]);
////            $product->stock = availableInventoryBalance($product->id, $store->id);
//            $product->discountable = !$product->parent->non_discountable;
//        }
//        return $products;
//    }

    public function getAllProductCategories(Request $request)
    {
        return ChartOfInventory::query()
            ->select(['id', 'name'])
            ->where(['rootAccountType' => 'FG', 'status' => 'active'])
            ->where('type', 'group')
            ->whereExists(function ($q) {
                $q->selectRaw('1')
                    ->from('chart_of_inventories as items')
                    ->whereColumn('items.parent_id', 'chart_of_inventories.id')
                    ->where('items.type', 'item');
            })
            ->orderBy('name')
            ->get();
    }

    public function getProductByNameSkuBarCode(Request $request)
    {
        $searchString = $request->search_term;
        $matches = ChartOfInventory::query()
            ->where(['rootAccountType' => 'FG', 'status' => 'active', 'type' => 'item'])
            ->where('name', 'like', '%' . $searchString . '%')
            ->limit(2)
            ->get();
        if ($matches->count() == 1) {
            return $matches->first();
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
        $data = Customer::query()
            ->select(['id', 'name', 'email', 'mobile', 'address'])
            ->where(['status' => 'active', 'type' => 'regular']);

        if ($request->filled('search_string')) {
            $search = $request->search_string;
            $data->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('mobile', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        return $data->latest()->take(20)->get();
    }

    public function getCustomerByNumber(Request $request)
    {
        $data = Customer::with(['membership.memberType'])
            ->where('mobile', $request->number)
            ->first();

        if ($data) {
            $data->current_point = $data->membership ? $data->membership->point : 0;
            $data->minimum_purchase = $data->membership ? $data->membership->memberType->minimum_purchase : 0;
            $data->purchase_discount = $data->membership ? $data->membership->memberType->discount : 0;
            $data->reedemible_point = $data->membership ? round($data->currentReedemablePoint(), 2) : 0;
            $data->member_type_name = $data->membership ? $data->membership->memberType->name : '';
        }
        return $data;
    }

    public function getAllOrders(Request $request)
    {
        if (!auth()->user()?->employee?->outlet_id) {
            return response()->json([
                'data' => [],
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 20,
                'total' => 0,
                'has_more' => false,
            ]);
        }

        $perPage = min((int) $request->get('per_page', 20), 50);
        $page = max((int) $request->get('page', 1), 1);
        $outletId = auth()->user()->employee->outlet_id;

        $ordersQuery = Sale::query()
            ->select([
                'id', 'invoice_number', 'date', 'subtotal', 'discount', 'grand_total',
                'customer_id', 'created_at', 'outlet_id',
            ])
            ->where('outlet_id', $outletId)
            ->with([
                'customer:id,name,mobile,type',
                'items:id,sale_id,product_id,unit_price,quantity',
                'items.coi:id,name',
            ])
            ->withSum('items', 'quantity');

        if ($request->filled('inv')) {
            $ordersQuery->where('invoice_number', 'like', '%' . $request->inv . '%');
        } else {
            $ordersQuery->where('date', date('Y-m-d'));
        }

        $paginator = $ordersQuery->latest()->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'has_more' => $paginator->hasMorePages(),
        ]);
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
        if ($alreadyUsed && $alreadyUsed->already_used >= $alreadyUsed->max_use) {
            return response()->json(['success' => false, 'message' => 'You have already used this promo code the maximum allowed times. ']);
        }

        if (!$request->otp) {
            return response()->json(['success' => false, 'message' => 'OTP Required']);
        }
        $otp = VerifyOtp::where('mobile_number', $user->mobile)->where('otp_status', 'pending')->first();
        if (!$otp) {
            return response()->json(['success' => false, 'message' => 'OTP Not Matched. Please Resend OTP']);
        }
        if ($otp->otp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP']);
        }
        $otp->update([
            'otp_status' => 'verified'
        ]);

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
            Log::emergency("File:" . $error->getFile() . "Line:" . $error->getLine() . "Message:" . $error->getMessage());
            return response()->json(['success' => false, 'message' => 'Something Went Wrong']);
        }
        return response()->json(['success' => true, 'message' => 'Customer Updated']);
    }

    public function printInvoice($id)
    {
        $sale = Sale::find($id);
//        return $sale->membershipPointHistory[0];
        $message = 'Sold Goods are not returnable.';
        if ($sale->preOrder) {
            $message = "Pre-order goods can be  cancelled before 24 hours of delivery.";
        }
        $sale->message = $message;
//        return view('pos.print.order', compact('sale'));
        $options = new Options();
        $options = ['chroot' => base_path()];
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('pos.print.order', compact('sale'))->render());


        $dompdf->render();
//        $output = $dompdf->output();
//        $pdfFilePath = storage_path('app/invoices/invoice_' . $id . '.pdf');
//        file_put_contents($pdfFilePath, $output);
        // Serve the PDF URL
//        return response()->file($pdfFilePath);


        // (Optional) Set paper size and orientation
//        $dompdf->setPaper('A4', $this->pageOrientation);
        // Render the HTML as PDF
//        $dompdf->render();
//        $output = $dompdf->output();
//        $pdfFilePath = storage_path('app/invoices/invoice_' . $id . '.pdf'); // Save path
////
//        file_put_contents($pdfFilePath, $output);
////
//        $this->print($pdfFilePath);
        return $dompdf->stream('order', ["Attachment" => false]);
    }

    public function storePreOrder(Request $request)
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
                        'mobile' => $request->customer_number,
                        'email' => null
                    ]);
                }
                $customer_id = $customer->id;
            }

            $outlet_id = \auth('web')->user()->employee->outlet_id;
            $outlet = Outlet::find($outlet_id);
            $store = Store::where(['doc_type' => 'outlet', 'doc_id' => $outlet->id])->first();

            $orderData = $request->order_data;
            $order = new PreOrder();
            $order->order_number = generateUniqueUUID($outlet_id, PreOrder::class, 'order_number');
            $order->order_date = $selectedDate;
            $order->subtotal = $request->sub_total;
            $order->discount = $request->discount;
            $order->grand_total = $request->grand_total;
            $order->customer_id = $customer_id;
            $order->created_by = auth('web')->user()->id;

            $order->advance_amount = $orderData['advance_payment'];
            $order->remark = $orderData['comment'];
            $order->order_from = $orderData['order_from'];
            $order->paid_by = $orderData['paid_by'];
            $order->delivery_date = Carbon::parse($orderData['delivery_date'])->format('Y-m-d');
            $order->customer_number = $request->customer_number;
            $order->outlet_id = $outlet_id;
            $order->save();

            $products = $request->get('products');
            $productIds = collect($products)->pluck('id')->filter()->unique()->values();
            $stockByProduct = $productIds->isEmpty()
                ? collect()
                : getInventoryQuantities($productIds, $store->id);
            foreach ($products as $row) {
                $row['coi_id'] = $row['id'];
                $row['unit_price'] = $row['price'];
                $currentStock = $stockByProduct[$row['coi_id']] ?? 0;
                if ($currentStock < $row['quantity']) {
                    DB::rollBack();
                    Toastr::error('Delivery Quantity cannot more then ' . $currentStock . ' !', '', ["progressBar" => true]);
                    return back();
                }
                $stockByProduct[$row['coi_id']] = $currentStock - $row['quantity'];

                $order->items()->create($row);
            }
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Log::emergency("File:" . $error->getFile() . "Line:" . $error->getLine() . "Message:" . $error->getMessage());
            return response()->json(['message' => $error->getMessage()], 500);
        }
        return response()->json(['message' => 'success', 'sale' => $order]);
    }

    public function getAllPreOrders()
    {
        $query = PreOrder::query()
            ->with([
                'items:id,pre_order_id,coi_id,quantity,unit_price',
                'items.product:id,name',
                'customer:id,name,mobile',
                'sale:id,invoice_number,created_at',
            ])
            ->withSum('items', 'quantity');

        if ($outletId = auth()->user()?->employee?->outlet_id) {
            $query->where('outlet_id', $outletId);
        }

        return $query->latest()
            ->limit(50)
            ->get()
            ->map(function ($order) {
                $order->status = $order->sale_id ? 'Delivered' : '';
                $order->backgroundColor = $order->sale_id ? '#e5e5e5' : '';
                $order->delivered_at = $order->sale_id ? $order->sale->readable_sell_date_time : '';
                $order->invoice_number = $order->sale_id ? $order->sale->invoice_number : '';
                return $order;
            });
    }

    public function getReturnNumberValue(Request $request)
    {
        $ret = SalesReturn::where('uid', $request->returnNumber)->first();
        if ($ret) {
            $obj = new \stdClass();
            $obj->amount = $ret->grand_total;
        } else {
            return response()->json(['success' => false, 'message' => 'Return Number Not Found'], 404);
        }

        return $obj;
    }

    public function print($pdfFilePath)
    {
        $printerName = $this->getDefaultPrinter();
        $connector = new CupsPrintConnector($printerName);
        $printer = new Printer($connector);

        // Start printing
        try {
            exec("lp -d " . escapeshellarg($printerName) . " " . escapeshellarg($pdfFilePath));
//            $printer->cut();
        } catch (\Exception $e) {
            return "Failed to print: " . $e->getMessage();
        } finally {
            $printer->close();
        }
        unlink($pdfFilePath);
    }


    public function getDefaultPrinter()
    {
        $os = PHP_OS_FAMILY;

        // Initialize output
        $output = [];

        if ($os === 'Linux') {
            // Command for Linux
            exec("lpstat -d", $output);
            return count($output) > 0 ? trim(str_replace("system default destination:", "", $output[0])) : null;

        } elseif ($os === 'Windows') {
            // Command for Windows
            exec("wmic printer get name", $output);
            return isset($output[1]) ? trim($output[1]) : null; // Assuming the first line after header is the default printer

        } elseif ($os === 'Darwin') {
            // Command for macOS
            exec("lpstat -d", $output);
            return count($output) > 0 ? trim(str_replace("system default destination:", "", $output[0])) : null;

        } else {
            return null; // Unsupported OS
        }
    }
}
