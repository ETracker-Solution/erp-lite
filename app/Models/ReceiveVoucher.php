<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveVoucher extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'amount' => 'float',
    ];

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'credit_account_id', 'id');
    }

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'debit_account_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function amountInWords(): string
    {
        if (!class_exists(\NumberFormatter::class)) {
            return number_format((float) $this->amount, 2) . ' Only';
        }

        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $taka = (int) floor((float) $this->amount);
        $paisa = (int) round(((float) $this->amount - $taka) * 100);

        $words = ucwords($formatter->format($taka)) . ' Taka';
        if ($paisa > 0) {
            $words .= ' and ' . ucwords($formatter->format($paisa)) . ' Paisa';
        }

        return $words . ' Only';
    }

    /**
     * Next voucher no in format RV:MM-YYYY-#### (month sequence).
     */
    public static function nextUid(?\DateTimeInterface $date = null): string
    {
        $date = $date ? \Carbon\Carbon::instance(\DateTimeImmutable::createFromInterface($date)) : now();
        $prefix = sprintf('RV:%s-%s-', $date->format('m'), $date->format('Y'));

        $latest = static::query()
            ->where('uid', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('uid');

        $seq = 1;
        if ($latest && preg_match('/(\d+)$/', $latest, $matches)) {
            $seq = ((int) $matches[1]) + 1;
        }

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
