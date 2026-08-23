<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FundTransferVoucher extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function (FundTransferVoucher $voucher) {
            if ($voucher->uid !== null && $voucher->uid !== '') {
                return;
            }

            $maxUid = (int) static::query()->max(DB::raw('CAST(NULLIF(uid, "") AS UNSIGNED)'));
            $maxId = (int) static::query()->max('id');
            $voucher->uid = (string) (max($maxUid, $maxId) + 1);
        });

        static::created(function (FundTransferVoucher $voucher) {
            if ($voucher->uid === null || $voucher->uid === '') {
                $voucher->forceFill(['uid' => (string) $voucher->id])->saveQuietly();
            }
        });
    }

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'credit_account_id');
    }

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'debit_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
