<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOthersOutletSaleRequest;
use App\Http\Requests\UpdateOthersOutletSaleRequest;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\Payment;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;


class OthersOutletSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $data = OthersOutletSale::where(['outlet_id' => \auth()->user()->employee->outlet_id])->latest();
        } else {
            $data = OthersOutletSale::latest();
        }
        if (\request()->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('others_outlet_sale.action', compact('row'));
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
        return view('others_outlet_sale.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serial_no = null;
        $user_store = null;
        if (!auth()->user()->is_super) {
            $user_store = Store::where(['doc_type' => 'outlet', 'doc_id' => \auth()->user()->employee->outlet_id])->first();
            $outlet_id = $user_store->doc_id;
            $serial_no = generateUniqueUUID($outlet_id, OthersOutletSale::class,'invoice_number');
        }
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => Store::where(['type' => 'FG', 'doc_type' => 'outlet'])->get(),
            'delivery_points' => Outlet::all(),
            'serial_no' => $serial_no,
            'customers' => Customer::where('status', 'active')->get(),
            'user_store' => $user_store,
            'invoice_number' => $serial_no

        ];
//        return $data;
        return view('others_outlet_sale.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOthersOutletSaleRequest $request)
    {
        $request->validate([
            'products' => 'array',
            'description' => 'nullable',
        ]);

        // dd( $request->all());
        try {
            DB::beginTransaction();

            $selectedDate = Carbon::parse($request->date)->format('Y-m-d');
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
            $outlet = Outlet::find($store->doc_id);
            $outlet_id = $outlet->id;
            $sale = new OthersOutletSale();
            $sale->invoice_number = generateUniqueUUID($outlet_id, OthersOutletSale::class,'invoice_number');
            $sale->subtotal = $request->subtotal;
            $sale->discount = $request->discount ?? 0;
            $sale->grand_total = $request->grandtotal;
            $sale->receive_amount = $request->receive_amount ?? 0;
            $sale->change_amount = $request->change_amount ?? 0;
            $sale->customer_id = $customer_id;
            $sale->date = $selectedDate;
//            $sale->description = $request->description;
            $sale->created_by = Auth::id();
            $sale->outlet_id = $outlet_id;
            $sale->delivery_point_id = $request->delivery_point_id;
            $sale->save();

            $products = $request->get('products');

            $salesAmount = $sale->grand_total;
            $avgProductionPrice = 0;

            foreach ($products as $row) {
                $row['product_id'] = $row['item_id'];
                $row['unit_price'] = $row['sale_price'];
                $currentStock = availableInventoryBalance($row['product_id'], $store->id);
                if ($currentStock < $row['quantity']) {
                    Toastr::error('Quantity cannot more then ' . $currentStock . ' !', '', ["progressBar" => true]);
                    return back();
                }

                $sale_item = $sale->items()->create($row);
                $sale_item['date'] = date('Y-m-d');
                $sale_item['coi_id'] = $row['product_id'];
                $sale_item['rate'] = averageFGRate($row['product_id']);
                $sale_item['amount'] = $sale_item['rate'] * $row['quantity'];
                $sale_item['store_id'] = $store->id;
                $avgProductionPrice += $sale_item['amount'];
            }
            $receive_amount = 0;
            foreach ($request->payment_methods as $paymentMethod) {
                $receive_amount += $paymentMethod['amount'];
                
            }
            $sale->receive_amount = $receive_amount;
            $sale->change_amount = $receive_amount - $sale->grand_total;
            $sale->save();
            DB::commit();

            Toastr::success('Sale Order Successful!.', '', ["progressBar" => true]);
            return redirect()->route('others-outlet-sales.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
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
        $otherOutletSale = OthersOutletSale::findOrFail(decrypt($id));
        return view('others_outlet_sale.show', compact('otherOutletSale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OthersOutletSale $othersOutletSale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOthersOutletSaleRequest $request, OthersOutletSale $othersOutletSale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OthersOutletSale $othersOutletSale)
    {
        //
    }
    public function pdfDownload($id)
    {
        $data = [
            'otherOutletSale' => OthersOutletSale::findOrFail(decrypt($id)),
        ];

        $pdf = PDF::loadView(
            'others_outlet_sale.pdf',
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
}
