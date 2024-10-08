<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\FGOpeningBalanceResource;
use App\Http\Resources\PaginateResource;
use App\Models\AccountTransaction;
use App\Models\ChartOfInventory;
use App\Models\FinishGoodsOpeningBalance;
use App\Models\RawMaterialOpeningBalance;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

class FGOpeningBalanceController extends Controller
{
    protected $base_model;

    public function __construct()
    {
        $this->base_model = FinishGoodsOpeningBalance::query();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('opening_balance.finish_goods.index');
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
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $alreadyExists = $this->base_model->where(['store_id' => $request->store_id, 'coi_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }

            $fgob = $this->base_model->create([
                'uid' => getNextId(FinishGoodsOpeningBalance::class),
                'date' => $request->date,
                'quantity' => $request->qty,
                'rate' => $request->rate,
                'amount' => $request->qty * $request->rate,
                'store_id' => $request->store_id,
                'coi_id' => $request->item_id,
                'remarks' => $request->remarks,
                'created_by' => auth()->user()->id,
            ]);
            addInventoryTransaction(1, 'FGOB', $fgob);

            addAccountsTransaction('FGOB', $fgob, getFGInventoryGLId(), getOpeningBalanceOfEquityGLId());
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Added',
            'success' => true
        ]);
    }

    public function list()
    {
        $fgob_balances = $this->base_model->with('chartOfInventory.unit', 'chartOfInventory.parent')->latest()->paginate(10);
        return response()->json(['success' => true, 'items' => new PaginateResource($fgob_balances, FGOpeningBalanceResource::class)]);
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
        DB::beginTransaction();
        try {
            $finishGoodsOpeningBalance = FinishGoodsOpeningBalance::find($id);
            $previous_uid = $finishGoodsOpeningBalance->uid;
            AccountTransaction::where(['doc_id' => $finishGoodsOpeningBalance->id, 'doc_type' => 'FGOB'])->delete();
            $finishGoodsOpeningBalance->inventoryTransaction()->delete();

            $finishGoodsOpeningBalance->delete();

            $alreadyExists = $this->base_model->where(['store_id' => $request->store_id, 'coi_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }

            $fgob = $this->base_model->create([
                'uid' => $previous_uid,
                'date' => $request->date,
                'quantity' => $request->qty,
                'rate' => $request->rate,
                'amount' => $request->qty * $request->rate,
                'store_id' => $request->store_id,
                'coi_id' => $request->item_id,
                'remarks' => $request->remarks,
                'created_by' => auth()->user()->id,
            ]);

            addInventoryTransaction(1, 'FGOB', $fgob);


            addAccountsTransaction('FGOB', $fgob, getFGInventoryGLId(), getOpeningBalanceOfEquityGLId());

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Update',
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $finishGoodsOpeningBalance = FinishGoodsOpeningBalance::find($id);
            AccountTransaction::where(['doc_id' => $finishGoodsOpeningBalance->id, 'doc_type' => 'FGOB'])->delete();
            $finishGoodsOpeningBalance->inventoryTransaction()->delete();
            $finishGoodsOpeningBalance->delete();
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'success' => false
            ]);
        }
        return response()->json([
            'message' => 'Update',
            'success' => true
        ]);
    }

    public function initialInfo()
    {
        return response()->json([
            'next_id' => getNextId(FinishGoodsOpeningBalance::class),
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'FG'])->get(),
            'stores' => \auth()->user() && \auth()->user()->employee && \auth()->user()->employee->outlet_id ? Store::query()->whereType('FG')->where(['doc_type'=>'outlet', 'doc_id'=>\auth()->user()->employee->outlet_id])->get() : Store::query()->whereType('FG')->get(),
            'success' => true
        ]);
    }

    public function sampleDownload()
    {
        try {
            $exportData = collect([
                [
                    'Date(Year-Month-Date)' => '2024-07-29',
                    'Store Name' => 'Store RM Royal Village',
                    'Group Name' => 'Cake',
                    'Item Name' => 'Chocolate',
                    'Quantity' => '100',
                    'Rate' => '120',
                    'Remarks' => 'In Any',
                ]
            ]);
            $fileName = "fg_ob_sample.xlsx";
            return (new \Rap2hpoutre\FastExcel\FastExcel($exportData))->download($fileName);
        } catch (\Exception $error) {
            return $error->getMessage();
        }
    }

    public function sampleUpload(Request $request)
    {
        try {
            $file = $request->file('file');
            $errorData = new Collection();

            (new FastExcel)->import($file, function ($line) use ($errorData) {
                $message = '';

                // Check for store
                $store = Store::query()->whereName($line['Store Name'])->first();
                if (!$store) {
                    $message = '| Invalid Store Name';
                    $line['Feedback'] = $message;
                    $errorData->push($line);
                    return; // Skip to the next iteration
                }

                // Check for group
                $group = ChartOfInventory::query()->where(['type' => 'group', 'rootAccountType' => 'FG'])->whereName($line['Group Name'])->first();
                if (!$group) {
                    $message = '| Invalid Group Name';
                    $line['Feedback'] = $message;
                    $errorData->push($line);
                    return; // Skip to the next iteration
                }

                // Check for item
                $item = ChartOfInventory::query()->where(['type' => 'item', 'rootAccountType' => 'FG', 'parent_id' => $group->id])->whereName($line['Item Name'])->first();
                if (!$item) {
                    $message = '| Invalid Item Name';
                    $line['Feedback'] = $message;
                    $errorData->push($line);
                    return; // Skip to the next iteration
                }

                $qty = $line['Quantity'] ?? 0;
                $rate = $line['Rate'] ?? 0;
                $date = $line['Date(Year-Month-Date)'] ?? '';

                // Check if the entry already exists
                $alreadyExists = $this->base_model->where(['store_id' => $store->id, 'coi_id' => $item->id])->exists();
                if ($alreadyExists) {
                    $message = '| Opening Balance Already Added';
                    $line['Feedback'] = $message;
                    $errorData->push($line);
                    return; // Skip to the next iteration
                }

                // If all conditions are met, create the entry
                if (!$alreadyExists && ($qty > 0) && $rate && $date) {
                    DB::beginTransaction();
                    try {
                        $rmob = $this->base_model->create([
                            'uid' => getNextId(FinishGoodsOpeningBalance::class),
                            'date' => $date,
                            'quantity' => $qty,
                            'rate' => $rate,
                            'amount' => $qty * $rate,
                            'store_id' => $store->id,
                            'coi_id' => $item->id,
                            'remarks' => $line['Remarks'] ?? null,
                            'created_by' => auth()->user()->id,
                        ]);

                        addInventoryTransaction(1, 'FGOB', $rmob);
                        addAccountsTransaction('FGOB', $rmob, getRMInventoryGLId(), getOpeningBalanceOfEquityGLId());

                        DB::commit();
                    } catch (\Exception $error) {
                        DB::rollBack();
                        Log::error($error->getMessage());
                        $line['Feedback'] = $error->getMessage();
                        $errorData->push($line);
                    }
                } else {
                    $line['Feedback'] = $message;
                    $errorData->push($line);
                }
            });

            // If there were any errors, generate an Excel file with failed records
            if (count($errorData) > 0) {
                $fileName = time() . "_fg_ob_creation_failed_jobs.xlsx";
                Toastr::warning('Excel File Upload Failed', 'Warning');
                return (new \Rap2hpoutre\FastExcel\FastExcel($errorData->all()))->download($fileName);
            }
        } catch (\Exception $error) {
            Log::error($error);
        }

        Toastr::success('FG OB Uploaded Successfully', 'Success');
        return redirect()->back();
    }

}
