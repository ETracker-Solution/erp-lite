<?php

namespace App\Http\Controllers;

use App\Classes\RequisitionNumber;
use App\Http\Requests\StoreRMRequisitionRequest;
use App\Http\Requests\UpdateRMRequisitionRequest;
use App\Models\ChartOfInventory;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class RMRequisitionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\request()->ajax()) {
            $data = Requisition::where('type', 'RM')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('rm_requisition.action', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->rawColumns(['action', 'created_at', 'status'])
                ->make(true);
        }
        return view('rm_requisition.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' => Store::where(['type' => 'RM','doc_type'=>'ho'])->get(),
            'serial_no' => RequisitionNumber::serial_number()
        ];
        return view('rm_requisition.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRMRequisitionRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $requisition = Requisition::query()->create($data);
            $products = $request->get('products');
            foreach ($products as $row) {
                $requisition->items()->create($row);
            }
            DB::commit();
            Toastr::success('RM Requisition Entry Successful!.', '', ["progressBar" => true]);
            return redirect()->route('rm-requisitions.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            Toastr::info('Something went wrong!.', '', ["progressbar" => true]);
            return back();
        }
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
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' => Store::where(['type' => 'RM'])->get(),
            'requisition' => Requisition::find(decrypt($id))
        ];
        return view('rm_requisition.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRMRequisitionRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $requisition= Requisition::find($id);
            $requisition->update($validated);
            RequisitionItem::where('requisition_id', $requisition->id)->delete();
            $products = $request->get('products');
            foreach ($products as $row) {
                $requisition->items()->create($row);
            }


            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }

        Toastr::success('RM Requisition Updated Successful!.', '', ["progressbar" => true]);
        return redirect()->route('rm-requisitions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Requisition::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Requisition Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('rm-requisitions.index');
    }
}
