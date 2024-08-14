<?php


/**
 * @throws Exception
 */
function pointEarnAndUpgradeMember($sale_id, $customer_id, $grand_total)
{
    $membership = customerCurrentMembership($customer_id, $grand_total);

    if ($membership) {
        $point = membershipPointToEarn($membership->member_type_id, $grand_total);
        if ($point > 0) {
            \App\Models\MembershipPointHistory::create([
                'sale_id' => $sale_id,
                'customer_id' => $customer_id,
                'point' => $point,
                'member_type_id' => $membership->member_type_id
            ]);
            $membership->increment('point', $point);
            $latest_membership = membershipByPoint($membership->point);
            if ($latest_membership && $membership->member_type_id != $latest_membership->id) {
                $membership->update(['member_type_id' => $latest_membership->id]);
            }
        }
    }
}

/**
 * @throws Exception
 */
function customerCurrentMembership($customer_id, $totalBill = 0)
{
    $data = \App\Models\Membership::where('customer_id', $customer_id)->first();
    if (!$data) {
        $minimum_purchase_amount_to_be_a_member = 100;
        if ($minimum_purchase_amount_to_be_a_member <= $totalBill) {
            $data = \App\Models\Membership::create([
                'member_type_id' => 1,
                'customer_id' => $customer_id,
            ]);
        }

    }
    return $data;
}

function membershipPointToEarn($member_type_id, $total_bill)
{
    $data = \App\Models\MemberPoint::where('member_type_id', $member_type_id)->first();
    $point = 0;
    if ($data && $data->per_amount > 0 && $data->point > 0) {
        $minimum_purchase_amount = $data->per_amount;
        if ($total_bill >= $minimum_purchase_amount) {
            $amount = (int)$total_bill / $minimum_purchase_amount;
            $point = $amount * $data->point;
        }
    }
    return $point;
}

/**
 * @throws Exception
 */
function membershipByPoint($point)
{
    $data = \App\Models\MemberType::where(function ($q) use ($point) {
        return $q->where('from_point', '<=', $point)->where('to_point', '>=', $point);
    })->first();
    if ($data) {
        return $data;
    }
}

function redeemPoint($sale_id, $customer_id,$point)
{
    $membership = customerCurrentMembership($customer_id);
    if ($membership) {
        \App\Models\MembershipPointHistory::create([
            'sale_id' => $sale_id,
            'customer_id' => $customer_id,
            'point' => $point * (-1),
            'member_type_id' => $membership->member_type_id
        ]);
//        $membership->decrement('point', $point);
    }
}

function getPreviousPointBeforeSale($sale_id,$customer_id)
{
    $last_point = \App\Models\MembershipPointHistory::where('sale_id',$sale_id)->where('member_type_id', '!=',1)->first();
    if ($last_point){
       return \App\Models\MembershipPointHistory::where('sale_id',$sale_id)->where('member_type_id', '!=',1)->where('customer_id',$customer_id)->where('created_at', '<',$last_point->created_at)->sum('point');
    }
    return 0;
}
