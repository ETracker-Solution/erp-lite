<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsumptionRequest;
use App\Http\Requests\UpdateConsumptionRequest;
use App\Models\Batch;
use App\Models\ChartOfInventory;
use App\Models\Consumption;
use App\Models\Product;
use App\Models\Production;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Yajra\DataTables\Facades\DataTables;

class ConsumptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\request()->ajax()) {
            $consumptions = Consumption::latest();
            return DataTables::of($consumptions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return view('consumption.action', compact('row'));
                })
                ->editColumn('status', function ($row) {
                    return showStatus($row->status);
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('consumption.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $serial_count = Consumption::latest()->first() ? Consumption::latest()->first()->id : 0;
        $serial_no = $serial_count + 1;
        $data = [
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' => Store::where(['type' => 'RM'])->get(),
            'batches' => Batch::all(),
            'serial_no' => $serial_no,
        ];

        return view('consumption.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreConsumptionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConsumptionRequest $request)
    {
        //dd($request->all());
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            if (count($validated['materials']) < 1) {
                Toastr::info('At Least One Product Required.', '', ["progressBar" => true]);
                return back();
            }
            $validated['created_by'] = authUser(true);
            $consumption = Consumption::create($validated);
            foreach ($validated['materials'] as $product) {
                $consumption->items()->create($product);
                factoryStockOut(\auth('factory')->user()->production_house_id, $product['product_id'], $product['unit_price'] ?? 0, $product['quantity'], 'raw');
            }
            Production::updateOrCreate(['id' => $validated['production_id']], ['status' => 'adjusted']);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            Toastr::info('Something went wrong!.', '', ["progressBar" => true]);
            return back();
        }
        Toastr::success('Consumption Created Successfully!.', '', ["progressBar" => true]);
        return redirect()->route('factory.consumptions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Consumption $consumption
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Consumption $consumption)
    {
        return view('factory.consumption.show', compact('consumption'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Consumption $consumption
     * @return \Illuminate\Http\Response
     */
    public function edit(Consumption $consumption)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateConsumptionRequest $request
     * @param \App\Models\Consumption $consumption
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateConsumptionRequest $request, Consumption $consumption)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Consumption $consumption
     * @return \Illuminate\Http\Response
     */
    public function destroy(Consumption $consumption)
    {
        //
    }

    public function consumptionPdf($id)
    {
        $stock_adjust = Consumption::findOrFail(decrypt($id));
        $data = [
            'stock_adjust' => $stock_adjust,
        ];

        $pdf = Pdf::loadView(
            'factory.consumption.pdf',
            $data,
            [],
            [
                'format' => 'A4-P',
                'orientation' => 'P',
                'margin-left' => 1,

                '', // mode - default ''
                '', // format - A4, for example, default ''
                0, // font size - default 0
                '', // default font family
                1, // margin_left
                1, // margin right
                1, // margin top
                1, // margin bottom
                1, // margin header
                1, // margin footer
                'L', // L - landscape, P - portrait

            ]
        );
        $name = \Carbon\Carbon::now()->format('d-m-Y');

        return $pdf->stream($name . '.pdf');
    }
}
