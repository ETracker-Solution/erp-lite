<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSystemSettingRequest;
use App\Http\Requests\UpdateSystemSettingRequest;
use App\Models\SystemSetting;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class SystemSettingController extends Controller
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
        return view('system_settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSystemSettingRequest $request)
    {
        $settings =  $request->settings;
        DB::beginTransaction();
        try {
            foreach($settings as $key=>$value){        
                
                if($key == 'fav_icon' && $request->hasFile('settings.fav_icon')){
                    $image = $settings['fav_icon'];
                    $value = getFileNameAfterImageUpload($image);
                }   

                if($key == 'company_logo' && $request->hasFile('settings.company_logo')){
                    $image = $settings['company_logo'];
                    $value = getFileNameAfterImageUpload($image);
                }
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSystemSettingRequest $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
