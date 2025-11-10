<?php

namespace App\Http\Controllers;

use App\Models\VerifyOtp;
use Illuminate\Http\Request;

class VerifyOtpController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (!$request->mobile_number) {
            return response()->json(['success' => false, 'message' => 'Mobile Number Required']);
        }
        if (!$request->otp) {
            return response()->json(['success' => false, 'message' => 'OTP Required']);
        }

        if ($request->type == 'regular_discount'){
            $otp = VerifyOtp::where('mobile_number', $request->mobile_number)->where('otp_type', 'regular_discount')->where('otp_status', 'pending')->first();
        }else{
            $otp = VerifyOtp::where('mobile_number', $request->mobile_number)->where('otp_status', 'pending')->first();
        }

        if (!$otp) {
            return response()->json(['success' => false, 'message' => 'Invalid Mobile Number']);
        }
        if ($otp->otp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP']);
        }
        $otp->update([
            'otp_status' => 'verified'
        ]);
        return response()->json(['success' => true, 'message' => 'OTP Verified Successfully']);
    }
}
