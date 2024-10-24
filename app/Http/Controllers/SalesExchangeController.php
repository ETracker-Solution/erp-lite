<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesExchangeRequest;
use App\Http\Requests\UpdateSalesExchangeRequest;
use App\Models\Customer;
use App\Models\OthersOutletSale;
use App\Models\Outlet;
use App\Models\Sale;
use App\Models\SalesExchange;
use App\Models\Store;
use Yajra\DataTables\Facades\DataTables;

class SalesExchangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id) {
            $data = SalesExchange::with('deliveryPoint', 'outlet')->where(['delivery_point_id' => \auth()->user()->employee->outlet_id])->latest();
        } elseif (\auth()->user()->is_super) {
            $data = SalesExchange::with('deliveryPoint', 'outlet')->latest();
        } else {
            $data = null;
        }
        if (\request()->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('sales_exchange.action', compact('row'));
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
        return view('sales_exchange.index');
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
        $sales = Sale::all();
        $data = [
            'customers' => Customer::where('status', 'active')->get(),
            'sales' => $sales,
            'stores' => Store::where(['type' => 'FG', 'doc_type' => 'outlet'])->get(),
            'delivery_points' => Outlet::all(),
            'user_store' => $user_store,

        ];
        return view('sales_exchange.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalesExchangeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesExchange $salesExchange)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesExchange $salesExchange)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalesExchangeRequest $request, SalesExchange $salesExchange)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesExchange $salesExchange)
    {
        //
    }
}
