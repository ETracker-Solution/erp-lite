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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;
use Yajra\DataTables\Facades\DataTables;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
class SalesDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
        //     $data = OthersOutletSale::with('deliveryPoint', 'outlet')->where(['delivery_point_id' => \auth()->user()->employee->outlet_id])->latest();
        // } elseif (\auth()->user()->is_super) {
        //     $data = OthersOutletSale::with('deliveryPoint', 'outlet')->latest();
        // } else {
        //     $data = null;
        // }
        if (\request()->ajax()) {
            $data = $this->getFilteredData();
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
                    return number_format(max($row->grand_total - ($row->receive_amount + $row->delivery_point_receive_amount),0), 2);
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
        // return $this->print();
        $user_store = null;
        if (!auth()->user()->is_super) {
            $user_store = Store::where(['doc_type' => 'outlet', 'doc_id' => \auth()->user()->employee->outlet_id,'status'=>'active'])->first();
            $outlet_id = $user_store->doc_id;
        }
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $sales = OthersOutletSale::with('deliveryPoint', 'outlet')->where(['delivery_point_id' => \auth()->user()->employee->outlet_id])->where('status', '!=', 'delivered')->latest()->get();
        } elseif (\auth()->user()->is_super) {
            $sales = OthersOutletSale::with('deliveryPoint', 'outlet')->where('status', '!=', 'delivered')->latest()->get();
        }
        $data = [
            'customers' => Customer::where('status', 'active')->get(),
            'sales' => $sales,
            'stores' => Store::where(['type' => 'FG', 'doc_type' => 'outlet','status'=>'active'])->get(),
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

            if ($originalSale->status === 'delivered') {
                Toastr::error('This sale has already been processed!', '', ["progressBar" => true]);
                return back();
            }

            $originalSaleItems = $originalSale->items;

            $store = Store::find($request->store_id);

            $main_outlet = $originalSale->outlet;

            $delivery_outlet = Outlet::find($originalSale->delivery_point_id);
            $delivery_store_id = $delivery_outlet->stores()->where('status','active')->first()->id;

            $mail_outlet_store_id = $main_outlet->stores()->first()->id;


            $receiveData = [];
            $tq = 0;

            $originalSale->update([
                'status' => 'processing' // Temporary status
            ]);
            // transfer Stock

            $outlet = Outlet::find($store->doc_id);
            $outlet_id = $outlet->id;
            $sale = Sale::where('invoice_number',$originalSale->invoice_number)->first();
            $sale->date = date('Y-m-d');
            $salesAmount = $sale->grand_total;

            if ($sale->preOrder || ($sale->outlet_id != $sale->delivery_point_id)) {
                foreach ($originalSaleItems as $row) {
                    $row =  collect($row)->toArray();
                    $currentStock = availableInventoryBalance($row['product_id'], $delivery_store_id);
                    if ($currentStock < $row['quantity']) {
                        Toastr::error('Quantity cannot more then ' . $currentStock . ' !', '', ["progressBar" => true]);
                        return back();
                    }

                    $sale_item = $sale->items()->where('product_id',$row['product_id'])->first();
                    $sale_item['date'] = date('Y-m-d');
                    $sale_item['coi_id'] = $row['product_id'];
                    $sale_item['rate'] = averageFGRate($row['product_id']);
                    $sale_item['amount'] = $sale_item['rate'] * $row['quantity'];
                    $sale_item['store_id'] = $delivery_store_id;
                    $sale_item = (object)$sale_item;
                    addInventoryTransaction(-1, 'POS', $sale_item);

                }
                $receive_amount = $sale->receive_amount;

            }
            $delivery_receive = 0;

            $receivable_amount = $salesAmount - $sale->receive_amount;
            $receive_amount = 0;

            foreach ($request->payment_methods as $paymentMethod) {
                $delivery_receive += $paymentMethod['amount'];
            }
            $change_amount = $delivery_receive - $receivable_amount;
            foreach ($request->payment_methods as $paymentMethod) {
                $payment = Payment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $customer_id ?? null,
                    'payment_method' => $paymentMethod['method'],
                    'amount' => ($paymentMethod['method'] == 'cash' && $change_amount > 0) ? ($paymentMethod['amount'] - $change_amount) : $paymentMethod['amount'],
                ]);
                $sale->amount = $payment->amount;
                if ($paymentMethod['method'] == 'nexus') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'Nexus'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'pbl') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'PBL'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'due') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'Due'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'upay') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'upay'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'rocket') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'rocket'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'DBBL') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'DBBL'), getAccountsReceiveableGLId());
                }
                if ($paymentMethod['method'] == 'UCB') {
                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'UCB'), getAccountsReceiveableGLId());
                }
//                if ($paymentMethod['method'] == 'bank') {
//                    addAccountsTransaction('POS', $sale, outletTransactionAccount($outlet_id, 'bank'), getAccountsReceiveableGLId());
//                }
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

            $originalSale->update([
                'status'=>'delivered',
                'payment_status'=>'paid',
                'delivery_point_receive_amount'=>$delivery_receive
            ]);

            if ($delivery_receive > 0) {
                $sale->amount = $delivery_receive;
                addCustomerTransaction($sale, -1);
            }

//            $sale->amount = $salesAmount;
//            addAccountsTransaction('POS',$sale, getCashGLID(), getAccountsReceiveableGLId());
            DB::commit();

            Toastr::success('Sale Delivered Successful!.', '', ["progressBar" => true]);
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

    public function print()
    {
//        return phpinfo();
        $printer = null;

        try {
            $connector = new CupsPrintConnector($this->getDefaultPrinter());
            $printer = new Printer($connector);
            $printer->text("Invoice #" . 1 . "\n");
            $printer->text("Date: " . '2024-01-01' . "\n");
            $printer->text("Customer: " . 'Noman' . "\n");

            // Feed and cut the paper
            $printer->feed();
            $printer->cut();
        } catch (\Exception $e) {
            // Log or print the exception for detailed error information
            \Log::error('Printing failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
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

        // Extract the printer name from the output
        return count($output) > 0 ? trim(str_replace("system default destination:", "", $output[0])) : null;
    }

     private function getFilteredData()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $data = OthersOutletSale::with('deliveryPoint', 'outlet')->where(['delivery_point_id' => \auth()->user()->employee->outlet_id])->latest();
        } elseif (\auth()->user()->is_super || (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->user_of == 'ho')) {
            $data = OthersOutletSale::with('deliveryPoint', 'outlet')->latest();
        } else {
            $data = null;
        }

        if (\request()->filled(key: 'status')) {
            $data = $data->where('status', \request()->status);
        }
        if (\request()->filled('from_date') && \request()->filled('to_date')) {
            $from_date = Carbon::parse(request()->from_date)->format('Y-m-d');
            $to_date = Carbon::parse(request()->to_date)->format('Y-m-d');
            $data = $data->whereDate('date', '>=', $from_date)->whereDate('date', '<=', $to_date);
        }
        return $data->latest();
    }

}
