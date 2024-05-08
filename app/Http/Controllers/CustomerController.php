<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::all();
        if (\request()->ajax()) {
            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('customer.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at',compact('row'));
                })
                ->rawColumns(['action','created_at'])
                ->make(true);
        }
        return view('customer.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            Customer::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Customer Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('customers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::findOrFail(decrypt($id));
        return view('customer.show',compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail(decrypt($id));
        return view('customer.edit',compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCustomerRequest  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Customer::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Customer Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Customer::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Customer Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('customers.index');
    }

    public function trashList(){

        if (\request()->ajax()) {
            $customers = Customer::onlyTrashed()->latest();
            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('user.customer.trash-action-button',compact('row'));
                })
                // ->editColumn('status', function ($row) {
                //     return showStatus($row->status);
                // })
                ->addColumn('created_at', function ($row) {
                    return view('user.common.created_at', compact('row'));
                })
                ->rawColumns(['action','created_at'])
                ->make(true);
        }
        return view('customer.trash-list');
    }
    public function restore($id){
        $customer = Customer::withTrashed()->where('id',decrypt($id))->first();
        $customer->restore();
        Toastr::success('Customer has been Restored Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('customers.index');
    }
    public function permanentDelete($id){
        $customer = Customer::onlyTrashed()->where('id',decrypt($id))->first();
        $customer->forceDelete();
        Toastr::success('Customer has been Permanent Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('customers.index');
    }
}
