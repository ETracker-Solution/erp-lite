<?php

namespace App\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemConfigController extends Controller
{
    public function index()
    {
        return view('system_settings.index');
    }

    public function store(Request $request)
    {
        $settings =  $request->settings;
        DB::beginTransaction();
        try {
            foreach($settings as $key=>$value){
                storeValue($key, $value);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Config Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->back();
    }
}
