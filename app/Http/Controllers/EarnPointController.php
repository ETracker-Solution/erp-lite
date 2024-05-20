<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EarnPoint;
use App\Models\MembershipPointHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EarnPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\request()->ajax()) {
            $earnPoints = MembershipPointHistory::with('customer','sale')->where('point','>',0)->latest();
            return DataTables::of($earnPoints)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('earn-point.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at',compact('row'));
                })
                ->rawColumns(['action','created_at'])
                ->make(true);
        }
        return view('earn-point.index');
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
