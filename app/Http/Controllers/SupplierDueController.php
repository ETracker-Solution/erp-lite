<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierDueController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Supplier::where('status', 'active')->latest()->get();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('due_amount', function ($row) {
                     return $row->supplierTransactions()->sum(\DB::raw('amount * transaction_type'));
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('supplier-dues.show', $row->id) . '" class="btn btn-info btn-sm" title="Details"><i class="fas fa-eye"></i> Show</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('supplier_due.index');
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        $transactions = SupplierTransaction::where('supplier_id', $id)->with('chartOfAccount')->get(); 
        
        return view('supplier_due.show', compact('supplier', 'transactions'));
    }
}
