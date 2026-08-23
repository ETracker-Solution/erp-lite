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
        $user_store = null;

        if (!auth()->user()->is_super) {
            $user_store = Store::where([
                'doc_type' => 'outlet',
                'doc_id' => auth()->user()->employee->outlet_id,
                'status' => 'active'
            ])->first();
        }

        $data = [
            'customers' => [], // Will load via AJAX
            'sales' => [], // Will load via AJAX
            'stores' => Store::select('id', 'name')
                ->where([
                    'type' => 'FG',
                    'doc_type' => 'outlet',
                    'status' => 'active'
                ])
                ->get(),
            'delivery_points' => Outlet::select('id', 'name')->get(),
            'user_store' => $user_store,
        ];

        return view('sales_delivery.create', $data);
    }

    public function searchSales(Request $request)
    {
        $query = OthersOutletSale::query()
            ->select('id', 'invoice_number', 'grand_total', 'receive_amount', 'delivery_point_id', 'outlet_id')
            ->where('status', '!=', 'delivered');

        if ($request->has('search') && $request->search) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        if ($request->has('store_id') && $request->store_id) {
           $outlet_id = Store::find($request->store_id)->doc_id;
           $query->where('delivery_point_id', $outlet_id);
        }

        if (auth()->user()->employee && auth()->user()->employee->outlet_id && !auth()->user()->is_super) {
            $query->where('delivery_point_id', auth()->user()->employee->outlet_id);
        }

        $sales = $query->latest()->limit(50)->get();

        return response()->json($sales);
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
                DB::rollBack();
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
            // Prefer outlet + latest id: truncated invoice_numbers can collide across sales.
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

            // Deduct stock only when it was NOT taken at sale time (pre-order or other outlet).
            // sales.delivery_point_id does not exist — use OthersOutletSale columns.
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
                    'customer_id' => $customer_id,
                    'payment_method' => $paymentMethod['method'],
                    'amount' => ($paymentMethod['method'] == 'cash' && $change_amount > 0) ? ($paymentMethod['amount'] - $change_amount) : $paymentMethod['amount'],
                ]);
                $sale->amount = $payment->amount;
                postSalePaymentTransaction($sale, $paymentMethod['method'], $outlet_id, $customer_id);
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
            $data = $data->whereBetween('date', [$from_date, $to_date]);
        }
        return $data->latest();
    }

}
