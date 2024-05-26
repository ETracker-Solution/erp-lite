<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use App\Models\Requisition;
use App\Models\Store;
use Illuminate\Http\Request;

class RMRequisitionDeliveryController extends Controller
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
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' => Store::where(['type' => 'RM'])->get(),
            'requisitions' => Requisition::get()
        ];
        return view('requisition_delivery.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->all());
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
