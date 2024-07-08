<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\InventoryTransaction;
use App\Models\InventoryTransfer;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\Store;
use App\Models\TransferReceive;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SalesDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $data = OthersOutletSale::with('deliveryPoint', 'outlet')->where(['delivery_point_id' => \auth()->user()->employee->outlet_id])->latest();
        } elseif (\auth()->user()->is_super) {
            $data = OthersOutletSale::with('deliveryPoint', 'outlet')->latest();
        } else {
            $data = null;
        }
        if (\request()->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('sales_delivery.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->addColumn('due', function ($row) {
                    return number_format(($row->grand_total - ($row->receive_amount + $row->delivery_point_receive_amount)), 2);
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }
        return view('sales_delivery.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user_store = null;
        if (!auth()->user()->is_super) {
            $user_store = Store::where(['doc_type' => 'outlet', 'doc_id' => \auth()->user()->employee->outlet_id])->first();
            $outlet_id = $user_store->doc_id;
        }
        $sales = OthersOutletSale::with('deliveryPoint', 'outlet')->where('status', '!=', 'delivered')->latest()->get();
        $data = [
            'customers' => Customer::where('status', 'active')->get(),
            'sales' => $sales,
            'stores' => Store::where(['type' => 'FG', 'doc_type' => 'outlet'])->get(),
            'delivery_points' => Outlet::all(),
            'user_store' => $user_store,

        ];
        return view('sales_delivery.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            DB::beginTransaction();
            $selectedDate = Carbon::parse($request->date)->format('Y-m-d');

            $originalSale = OthersOutletSale::find($request->sale_id);
            $originalSaleItems = $originalSale->items;
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

            $store = Store::find($request->store_id);

            $main_outlet = $originalSale->outlet;

            $mail_outlet_store_id = $main_outlet->stores()->first()->id;

            $receiveData = [];
            $tq = 0;
            // transfer Stock
            $transferData = [
                'to_store_id' => $mail_outlet_store_id,
                'from_store_id' => $request->store_id,
                'date' => $selectedDate,
                'type' => 'FG',
                'reference_no' => 'nullable',
                'remark' => 'Sale Delivery Stock Transfer',
//                'created_by' => 'required',
            ];
            foreach ($originalSaleItems as $item) {
                $transferData['products'][] = [
                    'coi_id' => $item->product_id,
                    'rate' => averageFGRate($item->product_id),
                    'quantity' => $item->quantity,
                ];
                $tq +=$item->quantity;
            }


            $fGInventoryTransfer = InventoryTransfer::create($transferData);
            foreach ($transferData['products'] as $product) {
                $fGInventoryTransfer->items()->create($product);
            }

            //receive transfer
            $receiveData = [
                'from_store_id' => $transferData['from_store_id'],
                'to_store_id' => $transferData['to_store_id'],
                'inventory_transfer_id' => $fGInventoryTransfer->id,
                'date' => $selectedDate,
                'type' => 'FG',
                'reference_no' => 'nullable',
                'total_quantity' => $tq,
                'remark' => 'Sale Delivery Stock Receive',
//                'created_by' => 'required',
            ];

            foreach ($originalSaleItems as $item) {
                $receiveData['products'][] = [
                    'coi_id' => $item->product_id,
                    'rate' => averageFGRate($item->product_id),
                    'quantity' => $item->quantity,
                ];
                $tq +=$item->quantity;
            }

            $fGInventoryTransferReceive = TransferReceive::query()->create($receiveData);
            foreach ($receiveData['products'] as $product) {
                $fGInventoryTransferReceive->items()->create($product);
                // Inventory Transaction Effect
                InventoryTransaction::query()->create([
                    'store_id' => $fGInventoryTransferReceive->from_store_id,
                    'doc_type' => 'FGIT',
                    'doc_id' => $fGInventoryTransferReceive->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $fGInventoryTransferReceive->date,
                    'type' => -1,
                    'coi_id' => $product['coi_id'],
                ]);
                InventoryTransaction::query()->create([
                    'store_id' => $fGInventoryTransferReceive->to_store_id,
                    'doc_type' => 'FGIT',
                    'doc_id' => $fGInventoryTransferReceive->id,
                    'quantity' => $product['quantity'],
                    'rate' => $product['rate'],
                    'amount' => $product['quantity'] * $product['rate'],
                    'date' => $fGInventoryTransferReceive->date,
                    'type' => 1,
                    'coi_id' => $product['coi_id'],
                ]);
            }
            InventoryTransfer::where('id', $fGInventoryTransferReceive->id)->update(['status' => 'received']);


            $outlet = Outlet::find($store->doc_id);
            $outlet_id = $outlet->id;
            $sale = new Sale();
            $sale->invoice_number = generateUniqueUUID($main_outlet->id, Sale::class, 'invoice_number');
            // $sale->invoice_number = $request->invoice_number ?? InvoiceNumber::generateInvoiceNumber($outlet_id, $selectedDate);
            $sale->subtotal = $originalSale->subtotal;
            $sale->discount = $originalSale->discount ?? 0;
            $sale->grand_total = $originalSale->grand_total;
            $sale->receive_amount = $originalSale->grand_total ?? 0;
            $sale->change_amount =  0;
            $sale->customer_id = $customer_id;
            $sale->date = $selectedDate;
//            $sale->description = $request->description;
            $sale->created_by = Auth::id();
            $sale->outlet_id = $main_outlet->id;
            $sale->save();


            $salesAmount = $sale->grand_total;
            $avgProductionPrice = 0;

            foreach ($originalSaleItems as $row) {
                $row =  collect($row)->toArray();
                $currentStock = availableInventoryBalance($row['product_id'], $mail_outlet_store_id);
                if ($currentStock < $row['quantity']) {
                    Toastr::error('Quantity cannot more then ' . $currentStock . ' !', '', ["progressBar" => true]);
                    return back();
                }

                $sale_item = $sale->items()->create($row);
                $sale_item['date'] = date('Y-m-d');
                $sale_item['coi_id'] = $row['product_id'];
                $sale_item['rate'] = averageFGRate($row['product_id']);
                $sale_item['amount'] = $sale_item['rate'] * $row['quantity'];
                $sale_item['store_id'] = $mail_outlet_store_id;
                addInventoryTransaction(-1, 'POS', $sale_item);

                $avgProductionPrice += $sale_item['amount'];
            }
            $receive_amount = 0;
            foreach (json_decode($originalSale->payment_methods, true) as $paymentMethod) {
                $receive_amount += $paymentMethod['amount'];
                Payment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $customer_id ?? null,
                    'payment_method' => $paymentMethod['method'],
                    'amount' => $paymentMethod['amount'],
                ]);
                $sale->amount = $paymentMethod['amount'];
                if ($paymentMethod['method'] == 'bkash') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($main_outlet->id, 'bkash'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'cash') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($main_outlet->id,), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'point') {
                    redeemPoint($sale->id, $customer_id, $paymentMethod['amount']);
                    addAccountsTransaction('POS', $sale, getRewardGLID(), getAccountsReceiveableGLId());
                }
            }
            $delivery_receive = 0;
            foreach ($request->payment_methods as $paymentMethod) {
                $receive_amount += $paymentMethod['amount'];
                $delivery_receive += $paymentMethod['amount'];
                Payment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $customer_id ?? null,
                    'payment_method' => $paymentMethod['method'],
                    'amount' => $paymentMethod['amount'],
                ]);
                $sale->amount = $paymentMethod['amount'];
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
            pointEarnAndUpgradeMember($sale->id, $customer_id ?? null, $request->grandtotal);
            //End Loyalty Effect
            $sale->amount = $salesAmount;
            addAccountsTransaction('POS', $sale, getAccountsReceiveableGLId(), getIncomeFromSalesGLId());
            $sale->amount = $avgProductionPrice;
            addAccountsTransaction('POS', $sale, getCOGSGLId(), getFGInventoryGLId());
            $originalSale->update([
                'status'=>'delivered',
                'payment_status'=>'paid',
                'delivery_point_receive_amount'=>$delivery_receive
            ]);
//            $sale->amount = $salesAmount;
//            addAccountsTransaction('POS',$sale, getCashGLID(), getAccountsReceiveableGLId());
            DB::commit();

            Toastr::success('Sale Order Successful!.', '', ["progressBar" => true]);
            return redirect()->route('sales-deliveries.index');
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getItemData($id)
    {
        $sale = OthersOutletSale::with('deliveryPoint', 'outlet', 'items.coi', 'customer')->where('id', $id)->first();
        return $sale;
    }
}
