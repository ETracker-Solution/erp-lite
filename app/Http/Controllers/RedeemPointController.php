<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MembershipPointHistory;
use App\Models\RedeemPoint;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RedeemPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\request()->ajax()) {
            $redeemPoints = MembershipPointHistory::with('customer', 'sale')->where('point', '<', 0);
            $redeemPoints = $this->filter($redeemPoints);

            return DataTables::of($redeemPoints)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('redeem-point.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at'])
                ->make(true);
        }
        return view('redeem-point.index');
    }

    protected function filter($data)
    {
        if (request('date_range')) {
            $dateRange = [];
            if (str_contains(request('date_range'), ' to ')) {
                $dateRange = explode(' to ', request('date_range'));
            } elseif (str_contains(request('date_range'), ' - ')) {
                $dateRange = explode(' - ', request('date_range'));
            } else {
                $dateRange = [request('date_range'), request('date_range')];
            }

            if (isset($dateRange[0]) && isset($dateRange[1])) {
                $data->whereBetween('created_at', [$dateRange[0] . ' 00:00:00', $dateRange[1] . ' 23:59:59']);
            } elseif (isset($dateRange[0])) {
                $data->whereDate('created_at', $dateRange[0]);
            }
        }

        if (request()->filled('customer_name')) {
            $data->whereHas('customer', function ($query) {
                $query->where('name', 'like', '%' . request('customer_name') . '%');
            });
        }

        if (request()->filled('invoice_no')) {
            $data->whereHas('sale', function ($query) {
                $query->where('invoice_number', 'like', '%' . request('invoice_no') . '%');
            });
        }

        return $data->latest();
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
        //
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
