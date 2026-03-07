<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBatchRequest;
use App\Http\Requests\UpdateBatchRequest;
use App\Models\Batch;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Batch::query();

            $query = $this->filter($query, request());

            return DataTables::of($query->latest())
                ->addIndexColumn()
                ->addColumn('action', fn($row) => view('batch.action', compact('row')))
                ->addColumn('created_at', fn($row) => view('common.created_at', compact('row')))
                ->rawColumns(['action', 'created_at'])
                ->make(true);
        }

        $serial_count = Batch::latest()->first() ? Batch::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        return view('batch.index', compact('serial_no'));
    }

    private function filter($query, $request)
    {
        return $query
            ->when($request->date_range, function ($q) use ($request) {
                searchColumnByDateRange($q, 'date', $request->date_range);
            })
            ->when($request->batch_no, fn($q) => $q->where('batch_no', 'like', "%{$request->batch_no}%"))
            ->when($request->p_manager, fn($q) => $q->where('p_manager', 'like', "%{$request->p_manager}%"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBatchRequest $request)
    {
         $validated = $request->validated();

        DB::beginTransaction();
        try {
            Batch::create($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Batch Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('batches.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Batch $batch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $batch = Batch::findOrFail(decrypt($id));
        return view('batch.index', compact('batch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBatchRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            Batch::findOrFail($id)->update($validated);
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Batch Updated Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('batches.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            Batch::findOrFail(decrypt($id))->delete();
            DB::commit();
        } catch (\Exception $error) {
            DB::rollBack();
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Batch Deleted Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('batches.index');
    }
}
