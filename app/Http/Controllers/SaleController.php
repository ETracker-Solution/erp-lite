<?php

namespace App\Http\Controllers;

use App\Libraries\SaleUtil;
use App\Models\Category;
use App\Models\ChartOfInventory;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Production;
use App\Models\Sale;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::all();
        if (\request()->ajax()) {
            return DataTables::of($sales)
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

        $serial_count = Sale::latest()->first() ? Production::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => Store::where(['type' => 'FG'])->get(),
            'serial_no' => $serial_no,
            'customers' => Customer::where('status', 'active')->get(),

        ];
        return view('sale.create', $data);
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

//         dd( $request->all());
        try {
            DB::beginTransaction();

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
            $sale = new Sale();
            $sale->invoice_number = \App\Classes\SaleNumber::serial_number();
            $sale->subtotal = $request->subtotal;
            $sale->discount = $request->discount;
            $sale->grand_total = $request->grandtotal;
            $sale->receive_amount = $request->receive_amount;
            $sale->change_amount = $request->change_amount;
            $sale->customer_id = $customer_id;
            $sale->date = Carbon::now()->format('Y-m-d');
//            $sale->description = $request->description;
            $sale->created_by = Auth::id();
            $sale->outlet_id = $outlet_id;
            $sale->save();

            $products = $request->get('products');

            $salesAmount = $sale->grand_total;
            $avgProductionPrice = 0;

            foreach ($products as $row) {
                $row['product_id'] = $row['item_id'];
                $row['unit_price'] = $row['sale_price'];
                $currentStock = availableInventoryBalance($row['product_id']);
                if ($currentStock < $row['quantity']) {
                    Toastr::error('Quantity cannot more then ' . $currentStock . ' !', '', ["progressBar" => true]);
                    return back();
                }

                $sale_item = $sale->items()->create($row);
                $sale_item['date'] = date('Y-m-d');
                $sale_item['coi_id'] = $row['product_id'];
                $sale_item['rate'] = averageFGRate($row['product_id']);
                $sale_item['amount'] = $sale_item['rate'] * $row['quantity'];
                $sale_item['store_id'] = 4;
                addInventoryTransaction(-1, 'POS', $sale_item);

                $avgProductionPrice += $sale_item['amount'];
            }
            $receive_amount = $request->receive_amount;
            //Start Loyalty Effect
//            pointEarnAndUpgradeMember($sale->id, $customer_id ?? null, $request->grand_total);
            //End Loyalty Effect
            $sale->amount = $salesAmount;
            addAccountsTransaction('POS',$sale, getAccountsReceiveableGLId(), getIncomeFromSalesGLId());
            $sale->amount = $avgProductionPrice;
            addAccountsTransaction('POS',$sale, getCOGSGLId(), getFGInventoryGLId());
            $sale->amount = $salesAmount;
            addAccountsTransaction('POS',$sale, getCashGLID(), getAccountsReceiveableGLId());
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
    public function show(Sale $sale)
    {
        $item_details = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(DB::raw('sale_items.*,products.name,products.sku,sum(sale_items.sale_price * sale_items.quantity) as item_total,sum(sale_items.quantity) as item_quantity'))
            ->where('sale_items.sale_id', $sale->id)
            ->groupBy('sale_items.product_id')
            ->get();

        $data = [
            'model' => $sale,
            'item_details' => $item_details,

        ];
        //  return $data;
        return view('sale.show', $data);
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

    public function pdf($id)
    {
        $item_details = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(DB::raw('sale_items.*,categories.name as category_name,products.name,products.sku,sum(sale_items.sale_price * sale_items.quantity) as item_total,sum(sale_items.quantity) as item_quantity'))
            ->where('sale_items.sale_id', $id)
            ->groupBy('sale_items.product_id')
            ->get();
        $data = [
            'model' => Sale::find($id),
            'item_details' => $item_details,

        ];
        $config = ['instanceConfigurator' => function ($mpdf) {
            $mpdf->SetWatermarkText('Emotobazar');
            $mpdf->showWatermarkText = true;
            $mpdf->watermarkTextAlpha = 0.05;
            //         $mpdf->SetWatermarkImage('images/1.png');
            //         $mpdf->showWatermarkImage = true;
            //         $mpdf->watermarkImageAlpha = 0.02;

        }];
        $pdf = PDF::loadHtml(
            view('sale.pdf', $data),
            $config,
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

    public function pdfDownload($id)
    {
        $data = [
            'model' => Sale::find($id),

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

        return $pdf->download($name . '.pdf');
    }
}
