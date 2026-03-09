<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GlobalExports;
use App\Exports\CustomerDueExport;

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
                )
                ->having('due_amount', '!=', 0);
            // We can filter those with due > 0 if needed, but for now show all with their due.
            // Or better, filter in PHP if dataset is small, or use SQL.
            // Let's return all and let frontend show.

            $data = $this->filter($data);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('due_amount', function ($row) {
                    return $row->due_amount ?? 0;
                })
                ->filterColumn('due_amount', function ($query, $keyword) {
                    $query->whereHas('customerTransactions', function ($q) use ($keyword) {
                        $q->select(\DB::raw('SUM(amount * transaction_type)'))
                            ->havingRaw('SUM(amount * transaction_type) like ?', ["%{$keyword}%"]);
                    });
                })
                ->orderColumn('due_amount', function ($query, $order) {
                    $query->orderBy('due_amount', $order);
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

    protected function filter($data)
    {
        if (request()->filled('customer_name')) {
            $data->where('name', 'like', '%' . request('customer_name') . '%');
        }

        if (request()->filled('phone')) {
            $data->where('mobile', 'like', '%' . request('phone') . '%');
        }

        return $data->latest();
    }

    public function show($id)
    {
        $customer = Customer::with('sales:id,customer_id,invoice_number')
            ->findOrFail($id);
        $transactions = CustomerTransaction::where('customer_id', $id)->with('chartOfAccount')->get();

        // Calculate running balance if needed, or just list.
        // Let's pass $transactions.

        return view('customer_due.show', compact('customer', 'transactions'));
    }

    public function export()
    {
        $customers = Customer::where('status', 'active')
            ->select('id', 'name', 'mobile')
            ->whereHas('customerTransactions');

        $customers = $this->filter($customers);

        $customers = $customers->withSum(
                ['customerTransactions as due_amount' => function ($q) {
                    $q->select(\DB::raw('SUM(amount * transaction_type)'));
                }],
                ''
            )
            ->having('due_amount', '!=', 0)
            ->with(['customerTransactions' => function($q) {
                $q->select('id', 'customer_id', 'date', 'description', 'amount', 'transaction_type')
                  ->orderBy('date', 'asc')
                  ->orderBy('id', 'asc');
            }])
            ->get();

        return Excel::download(new CustomerDueExport($customers), 'customer_due_list.xlsx');
    }
}
