<?php

namespace App\Http\Controllers;

use App\Classes\AvailableProductCalculation;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\request()->ajax()) {
            $stocks = StockIn::with('product', 'product.category')
                ->select(
                    'product_id',
                    DB::raw('COUNT(*) as product_count'),
                    DB::raw('COALESCE(SUM(quantity), 0) - COALESCE((SELECT SUM(quantity) FROM stock_outs WHERE stock_outs.product_id = stock_ins.product_id), 0) as available_qty')
                )
                ->groupBy('product_id');
            return DataTables::of($stocks)
                ->addIndexColumn()
                ->addColumn('quantity', function ($row) {
                    return $row->available_qty ?? 0;
                })
                ->rawColumns(['quantity'])
                ->make(true);
        }
        return view('stock.index');
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
        //
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
}
