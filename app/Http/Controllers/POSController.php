<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class POSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'sidebar' => 'sidebar-collapse',
            'model' => new Sale(),
            'categories' => Category::all(),
//razib
            'customers' => Customer::all(),
        ];
        return view('pos.create', $data);
    }
    public function fetchSubCategoryProductInfo($id)
    {
        $subCategory = SubCategory::where('category_id', $id)->get();
        if (count($subCategory) > 0) {
            # code...
            $products = Product::where(['status' => 'active', 'category_id' => $id])->get();
        } else {
            $products = Product::where(['status' => 'active', 'category_id' => $id])->get();
        }

        $data = [

            'subCategory' => $subCategory,
            'products' => $products,
        ];

        return $data;
    }
    public function fetch_sub_category_wise_product_info($catId, $subCatId)
    {
        $product_items = [];
        $items = Product::where(['status' => 'active', 'category_id' => $catId, 'sub_category_id' => $subCatId])->get();
        foreach ($items as $key => $item) {
            $product_items[$key] = [
                'id' => $item->id,
                'product_name' => $item->name,
                'available_qty' => \App\Classes\AvailableProductCalculation::product_id($item->id),
            ];
        }
        $products = Product::where(['status' => 'active', 'category_id' => $catId, 'sub_category_id' => $subCatId])->get();
        $data = [
            'items' => $product_items,
            'products' => $products,
        ];

        return $data;
    }
    public function fetch_product_info($id)
    {
        return Product::with('unit')->findOrFail($id);
    }
    public function fetch_all_product()
    {
        $products= Product::with('unit')->get();
        //  $products= Product::with('unit')->paginate(10);

        $data = [
            'products' => $products,
        ];
        return response()->json($data);
    }

    public function getItemBySearchKeyword($keyword)
    {

        $items = Product::where('name', 'LIKE', $keyword . '%')
            ->orWhere('code', 'LIKE', $keyword . '%')
            ->take(5)
            ->get();

        return response()->json($items);

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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'products' => 'array',
            'description' => 'nullable',
        ]);
        DB::beginTransaction();

        // dd( $request->all());

        $sale = new Sale();
        $sale->invoice_number = "SAL-" .\App\Classes\SaleNumber::serial_number();

        $sale->subtotal = $request->subtotal;
        $sale->discount = $request->discount;
        $sale->grandtotal = $request->grandtotal;
        $sale->receive_amount = $request->receive_amount;
        $sale->change_amount = $request->change_amount;
        $sale->date = Carbon::now()->format('Y-m-d');
        $sale->remark = $request->remark;
        $sale->creator_user_id = Auth::id();
        $sale->save();
        $products = $request->get('products');

        foreach ($products as $row) {

            $sale_item = new SaleItem();
            $sale_item->product_id = $row['product_id'];
            $sale_item->sale_price = $row['sale_price'];
            $sale_item->discount = $row['discount'] ?? 0;
            $sale_item->quantity = $row['quantity'];
            $sale_item->sale_id = $sale->id;
            $sale_item->save();

            $sale->stock_out_items()->create($row);
        }



        DB::commit();

        Toastr::success('Sale Order Successful!.', '', ["progressBar" => true]);
        return redirect()->route('sale.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
