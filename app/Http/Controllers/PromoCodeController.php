<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePromoCodeRequest;
use App\Http\Requests\UpdatePromoCodeRequest;
use App\Jobs\SendPromoCodeJob;
use App\Models\Customer;
use App\Models\MemberType;
use App\Models\PromoCode;
use App\Models\SmsTemplate;
use App\Services\NovocomSmsService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class PromoCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (\request()->ajax()) {
            $codes = PromoCode::latest();
            return DataTables::of($codes)
                ->addIndexColumn()
                ->editColumn('start_date', function ($row) {
                    return getTimeByFormat($row->start_date, 'd F Y');
                })
                ->editColumn('end_date', function ($row) {
                    return getTimeByFormat($row->end_date, 'd F Y');
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->addColumn('discount', function ($row) {
                    return $row->discount_type == 'fixed' ? $row->discount_value . ' BDT' : $row->discount_value . ' %';
                })
                ->addColumn('action', function ($row) {
                    return view('promo_code.action-button', compact('row'));
                })
                ->rawColumns(['status', 'action', 'discount'])
                ->make(true);
        }
        return view('promo_code.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $memberTypes = MemberType::all();
        $smsTemplates = SmsTemplate::getActiveTemplates();
        return view('promo_code.create', compact('memberTypes','smsTemplates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePromoCodeRequest $request, NovocomSmsService $service)
    {
        $validated =  $request->validated();
        DB::beginTransaction();
        try {
            if ($validated['discount_for'] == 'all_customers') {
                if (!array_key_exists('customers', $validated)) {
                    $validated['customers'] = Customer::pluck('id')->toArray();
                }
            }
            if ($validated['discount_for'] == 'non_member') {
                if (!array_key_exists('customers', $validated)) {
                    $validated['customers'] = Customer::whereDoesntHave('membership')->pluck('id')->toArray();
                }
            }
            if ($validated['discount_for'] == 'member') {
                $validated['member_types'] = implode(',', MemberType::whereIn('id', $validated['member_type'])->pluck('id')->toArray());
                if (!array_key_exists('customers', $validated)) {
                    $validated['customers'] = Customer::whereHas('membership', function ($q) use ($validated) {
                        $q->whereIn('member_type_id', $validated['member_type']);
                    })->pluck('id')->toArray();
                }
            }
            $promoCode = PromoCode::create($validated);

            foreach ($validated['customers'] as $customer) {
                $promoCode->customerPromoCodes()->create([
                    'customer_id' => $customer
                ]);
            }


            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Toastr::error('Something Went Wrong');
            return back();
        }
        Toastr::success('Promo Code Added');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $memberTypes = MemberType::all();
        $row = PromoCode::find(decrypt($id));
        return view('promo_code.edit', compact('row', 'memberTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePromoCodeRequest $request, $id)
    {
        $validated =  $request->validated();
        DB::beginTransaction();
        try {
            if ($validated['discount_for'] == 'all_customers') {
                if (!array_key_exists('customers', $validated)) {
                    $validated['customers'] = Customer::pluck('id')->toArray();
                }
            }
            if ($validated['discount_for'] == 'non_member') {
                if (!array_key_exists('customers', $validated)) {
                    $validated['customers'] = Customer::where('type', '!=', 'default')->whereDoesntHave('membership')->pluck('id')->toArray();
                }
            }
            if ($validated['discount_for'] == 'member') {
                $validated['member_types'] = implode(',', MemberType::whereIn('id', $validated['member_type'])->pluck('name')->toArray());
                if (!array_key_exists('customers', $validated)) {
                    $validated['customers'] = Customer::whereHas('membership', function ($q) use ($validated) {
                        $q->whereIn('member_type_id', $validated['member_type']);
                    })->pluck('id')->toArray();
                }
            }
            $promoCode = PromoCode::find(decrypt($id));
            $promoCode->update($validated);
            $promoCode->customerPromoCodes()->delete();
            foreach ($validated['customers'] as $customer) {
                $promoCode->customerPromoCodes()->create([
                    'customer_id' => $customer
                ]);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            Toastr::error('Something Went Wrong');
            return back();
        }
        Toastr::success('Promo Code Updated');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getCustomers(Request $request)
    {
        if ($request->discount_for == 'non_member') {
            $customers = Customer::where('type', '!=', 'default')->whereDoesntHave('membership');
        } else if ($request->discount_for == 'member') {
            $member_type = $request->member_type;
            if ($member_type) {
                $customers = Customer::where('type', '!=', 'default')->whereHas('membership', function ($q) use ($member_type) {
                    return $q->whereIn('member_type_id', $member_type);
                });
            } else {
                $customers = Customer::where('type', '!=', 'default')->whereHas('membership');
            }
        } else {
            $customers = Customer::where('type', '!=', 'default');
        }
        if ($request->term){
            $searchString = $request->term;
            $customers = $customers->where('name','like','%'.$searchString.'%')->orWhere('mobile','like','%'.$searchString.'%');
        }
        return $customers->select(DB::raw('CONCAT(mobile, " (", name, ")") as name'), 'id')->take(30)->get();
    }

    public function sendSms($id)
    {


        DB::beginTransaction();
        try {
            $promoCode = PromoCode::find(decrypt($id));

            $promoCode->customers()
                ->select('mobile')
                ->chunk(500, function ($customers) use ($promoCode) {
                    $numbers = $customers->pluck('mobile')->filter()->toArray();
                    if (!empty($numbers)) {
                        SendPromoCodeJob::dispatch($promoCode, $numbers);
                    }
                });

            $promoCode->increment('sms_count');

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception;
            Toastr::error('Something Went Wrong');
            return back();
        }
        Toastr::success('SMS is Sending.!!');
        return back();
    }

    public function fetchTemplates()
    {
        $templates = (new NovocomSmsService())->fetchTemplates();

        if (empty($templates)) {
            Log::warning('No SMS templates fetched from Novocom');
            return;
        }

        return response()->json([
            'success' => true,
            'templates' => count($templates)
        ]);

        Log::info('SMS templates synced successfully', [
            'count' => count($templates)
        ]);
    }
}
