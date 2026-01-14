<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerDueController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::where('status', 'active')->whereHas('customerTransactions')
                ->withSum(
                    ['customerTransactions as due_amount' => function ($q) {
                        $q->select(\DB::raw('SUM(amount * transaction_type)'));
                    }],
                    ''
                )->latest();
            // We can filter those with due > 0 if needed, but for now show all with their due.
            // Or better, filter in PHP if dataset is small, or use SQL.
            // Let's return all and let frontend show.

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('due_amount', function ($row) {
                    return $row->due_amount ?? 0;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('customer-dues.show', $row->id) . '" class="btn btn-info btn-sm" title="Details"><i class="fas fa-eye"></i> Show</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('customer_due.index');
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        $transactions = CustomerTransaction::where('customer_id', $id)->with('chartOfAccount')->get();

        // Calculate running balance if needed, or just list.
        // Let's pass $transactions.

        return view('customer_due.show', compact('customer', 'transactions'));
    }
}
