<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\ROMOpeningBalanceResource;
use App\Models\AccountTransaction;
use App\Models\ChartOfInventory;
use App\Models\InventoryTransaction;
use App\Models\Purchase;
use App\Models\RawMaterialOpeningBalance;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierGroup;
use App\Models\User\Department;
use App\Models\User\Designation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

class RMOpeningBalanceController extends Controller
{
    protected $base_model;

    public function __construct()
    {
        $this->base_model = RawMaterialOpeningBalance::query();
    }

    public function index()
    {
        return view('opening_balance.raw_materials.index');
    }

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

            $rmob = $this->base_model->create([
                'uid' => getNextId(RawMaterialOpeningBalance::class),
                'date' => $request->date,
                'quantity' => $request->qty,
                'rate' => $request->rate,
                'amount' => $request->qty * $request->rate,
                'store_id' => $request->store_id,
                'coi_id' => $request->item_id,
                'remarks' => $request->remarks,
                'created_by' => auth()->user()->id,
            ]);
            addInventoryTransaction(1, 'RMOB', $rmob);

            addAccountsTransaction('RMOB', $rmob, getRMInventoryGLId(), getOpeningBalanceOfEquityGLId());
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
        $rom_balances = $this->base_model->with('chartOfInventory.unit', 'chartOfInventory.parent')->latest()->paginate(10);
        return response()->json(['success' => true, 'items' => new PaginateResource($rom_balances, ROMOpeningBalanceResource::class)]);
    }

    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {
            $rawMaterialOpeningBalance = RawMaterialOpeningBalance::find($id);
            $previous_uid = $rawMaterialOpeningBalance->uid;
            AccountTransaction::where(['doc_id' => $rawMaterialOpeningBalance->id, 'doc_type' => 'RMOB'])->delete();
            $rawMaterialOpeningBalance->inventoryTransaction()->delete();

            $rawMaterialOpeningBalance->delete();

            $alreadyExists = $this->base_model->where(['store_id' => $request->store_id, 'coi_id' => $request->item_id])->exists();

            if ($alreadyExists) {
                return response()->json([
                    'message' => 'Opening Balance Already Added',
                    'success' => false
                ]);
            }

            $rmob = $this->base_model->create([
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

            addInventoryTransaction(1, 'RMOB', $rmob);


            addAccountsTransaction('RMOB', $rmob, getRMInventoryGLId(), getOpeningBalanceOfEquityGLId());

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

    public function destroy($id)
    {
        try {
            $rawMaterialOpeningBalance = RawMaterialOpeningBalance::find($id);
            $rawMaterialOpeningBalance->inventoryTransaction()->delete();
            $rawMaterialOpeningBalance->delete();
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
            'next_id' => getNextId(RawMaterialOpeningBalance::class),
            'groups' => ChartOfInventory::where(['type' => 'group', 'rootAccountType' => 'RM'])->get(),
            'stores' => Store::query()->whereType('RM')->get(),
            'success' => true
        ]);
    }

    public function sampleDownload()
    {
        try {
            $exportData = collect([
                [
                    'Date(Year-Month-Date)' => '2024-07-29',
                    'Store Name' => 'Store RM Head Office',
                    'Group Name' => 'Cake',
                    'Item Name' => 'Chocolate',
                    'Quantity' => '100',
                    'Rate' => '120',
                    'Remarks' => 'In Any',
                ]
            ]);
            $fileName = "rm_ob_sample.xlsx";
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
//                $line = array_filter($line);
                $store = Store::query()->whereName($line['Store Name'])->first();
                if (!$store) {
                    $message = '| Invalid Store Name';
                }

                $group = ChartOfInventory::query()->where(['type' => 'group', 'rootAccountType' => 'RM'])->whereName($line['Group Name'])->first();
                if (!$group) {
                    $message = '| Invalid Group Name';
                }

                $item = ChartOfInventory::query()->where(['type' => 'item', 'rootAccountType' => 'RM', 'parent_id' => $group->id])->whereName($line['Item Name'])->first();
                if (!$item) {
                    $message = '| Invalid Item Name';
                }

                $qty = $line['Quantity'] ?? 0;
                $rate = $line['Rate'] ?? 0;

                $date = $line['Date(Year-Month-Date)'] ?? '';

                $alreadyExists = $this->base_model->where(['store_id' => $store->id, 'coi_id' => $item->id])->exists();

                if ($alreadyExists) {
                    $message = '| Opening Balance Already Added';
                }

                if (!$alreadyExists && $store && $group && $item && ($qty > 0) && $rate && $date) {
                    DB::beginTransaction();
                    try {
                        $rmob = $this->base_model->create([
                            'uid' => getNextId(RawMaterialOpeningBalance::class),
                            'date' => $date,
                            'quantity' => $qty,
                            'rate' => $rate,
                            'amount' => $qty * $rate,
                            'store_id' => $store->id,
                            'coi_id' => $item->id,
                            'remarks' => $line ['Remarks'] ?? null,
                            'created_by' => auth()->user()->id,
                        ]);
                        addInventoryTransaction(1, 'RMOB', $rmob);

                        addAccountsTransaction('RMOB', $rmob, getRMInventoryGLId(), getOpeningBalanceOfEquityGLId());
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

            if (count($errorData) > 0) {
                $fileName = time() . "_rmob_creation_failed_jobs.xlsx";
                Toastr::warning('Excel File Upload Failed', 'Warning');
                return (new \Rap2hpoutre\FastExcel\FastExcel($errorData->all()))->download($fileName);
            }
        } catch (\Exception $error) {
            Log::error($error);
        }
        Toastr::success('RM OB Uploaded Successfully', 'Success');
        return redirect()->back();
    }
}
