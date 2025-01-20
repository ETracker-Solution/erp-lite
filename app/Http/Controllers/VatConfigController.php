<?php

namespace App\Http\Controllers;

use App\Models\ChartOfInventory;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class VatConfigController extends Controller
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
        return view('system_settings.vat_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $settings =  $request->settings;
        DB::beginTransaction();
        try {
            if (array_key_exists('global_vat_type', $settings) && array_key_exists('global_vat_amount', $settings)) {
                $inventories = ChartOfInventory::whereIn('type', ['item', 'group'])
                    ->where('rootAccountType', 'FG')->get();

                $inventories->each(function ($inventory) use ($settings) {
                    if ($inventory->type === 'group') {

                        $inventory->update([
                            'vat_type' => $settings['global_vat_type'],
                            'vat_amount' => $settings['global_vat_amount'],
                        ]);
                    } elseif ($inventory->type === 'item') {

                        $inventory->vat_type = $settings['global_vat_type'];
                        $inventory->vat_amount = $settings['global_vat_amount'];

                        if ($inventory->price !== null) {
                            $calculateVat = calculateVat($inventory->price, $settings['global_vat_type'], $settings['global_vat_amount']);
                        }

                        $inventory->base_price = $calculateVat['base_price'];
                        $inventory->vat = $calculateVat['vat'];
                        $inventory->total_price = $calculateVat['total'];

                        $inventory->save();
                    }
                });
            }

            foreach ($settings as $key => $value) {
                storeValue($key, $value);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Data Created Successfully!.', '', ["progressBar" => true]);
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
