<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\OutletTransactionConfig;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OuteletConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = Outlet::all();
        return view('outlet_config.create', compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->settings);
        $request->validate([
            'settings' => 'required|array',
        ]);
        foreach ($request->settings as $outletId => $item) {
            foreach($item as $key => $value) {
                $outletConfig = OutletTransactionConfig::where(['outlet_id'=>$outletId])->where('type',$key)->first();
                $outletConfig->update(
                    [
                        // 'outlet_id' => $outletId,
                        // 'type' => 'Bkash',
                        'coa_id' => $value,
                    ],
                );
                
            }
        }
        Toastr::success('Transaction configurations updated successfully!.', '', ["progressBar" => true]);
        return redirect()->back();
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
}
