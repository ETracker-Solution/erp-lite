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
            $data = Supplier::query()
                ->where('status', 'active')
                ->when($request->name, fn($q) => $q->where('name', 'like', "%{$request->name}%"))
                ->when($request->mobile, fn($q) => $q->where('mobile', 'like', "%{$request->mobile}%"))
                ->when($request->address, fn($q) => $q->where('address', 'like', "%{$request->address}%"));
            
            return DataTables::of($data->latest())
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
