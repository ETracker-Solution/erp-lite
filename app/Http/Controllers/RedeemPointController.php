<?php

namespace App\Http\Controllers;

use App\Models\MembershipPointHistory;
use Yajra\DataTables\Facades\DataTables;

class RedeemPointController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = MembershipPointHistory::query()
                ->select([
                    'membership_point_histories.id',
                    'membership_point_histories.point',
                    'membership_point_histories.customer_id',
                    'membership_point_histories.sale_id',
                    'membership_point_histories.created_at',
                ])
                ->with([
                    'customer:id,name,mobile',
                    'sale:id,invoice_number',
                ])
                ->where('point', '<', 0)
                ->latest('id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->editColumn('point', fn ($row) => number_format(abs((float) $row->point), 2))
                ->addColumn('action', function ($row) {
                    return view('redeem-point.action-button', compact('row'));
                })
                ->addColumn('created_at', function ($row) {
                    return view('common.created_at', compact('row'));
                })
                ->rawColumns(['action', 'created_at'])
                ->make(true);
        }

        return view('redeem-point.index');
    }

    public function show($id)
    {
        $redeemPoint = MembershipPointHistory::query()
            ->with([
                'customer:id,name,mobile,email',
                'sale:id,invoice_number,date,grand_total',
            ])
            ->where('point', '<', 0)
            ->findOrFail(decrypt($id));

        return view('redeem-point.show', compact('redeemPoint'));
    }
}
