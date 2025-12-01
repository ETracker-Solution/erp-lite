<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPromoCode;
use App\Models\PromoCode;
use App\Models\VerifyOtp;
use App\Services\NovocomSmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponCodeOtpController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, NovocomSmsService $service)
    {
        $now = Carbon::now()->format('Y-m-d');

        if (!$request->customer_number) {
            return response()->json(['success' => false, 'message' => 'Customer Number Required']);
        }

        if ($request->type == 'regular_discount'){
            return $this->regularOtp($request->customer_number, $service);
        }

        if (!$request->code) {
            return response()->json(['success' => false, 'message' => 'Code Required']);
        }

        $user = Customer::where('mobile', $request->customer_number)->first();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid Mobile Number']);
        }

        $code = PromoCode::where('code', $request->code)->where('start_date', '<=', $now)->where('end_date', '>=', $now)->first();
        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Invalid Code']);
        }

        $alreadyUsed = CustomerPromoCode::where(['customer_id' => $user->id, 'promo_code_id' => $code->id])->first();
        if ($alreadyUsed && $alreadyUsed->already_used >= $alreadyUsed->max_use) {
            return response()->json(['success' => false, 'message'=>'You have already used this promo code the maximum allowed times.']);
        }

        $otp = rand(1000, 9999);

        VerifyOtp::where('mobile_number', $user->mobile)->where('otp_type', 'verification')->delete();

        VerifyOtp::create([
            'mobile_number' => $user->mobile,
            'otp' => $otp,
        ]);

        try {
            $service->sendOtp($user->mobile, $otp);
            return response()->json(['success' => true, 'message' => 'OTP Sent Successfully']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP']);
        }

    }

    protected function regularOtp($customerNumber, NovocomSmsService $service)
    {
        $otp = rand(1000, 9999);

        VerifyOtp::where('mobile_number', $customerNumber)->where('otp_type', 'regular_discount')->delete();

        VerifyOtp::create([
            'mobile_number' => $customerNumber,
            'otp' => $otp,
            'otp_type'=> 'regular_discount'
        ]);

        try {
            $service->sendOtp($customerNumber, $otp);
            return response()->json(['success' => true, 'message' => 'OTP Sent Successfully']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP']);
        }
    }
}
