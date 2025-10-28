<?php

namespace App\Jobs;

use App\Models\PromoCode;
use App\Services\NovocomSmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SendPromoCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $promoCode;
    public $numbers;

    public function __construct(PromoCode $promoCode, array $numbers)
    {
        $this->promoCode = $promoCode;
        $this->numbers = $numbers;
    }

    public function handle(NovocomSmsService $service)
    {
        $code = $this->promoCode->code;
        $discountValue = $this->promoCode->discount_type == 'fixed'
            ? $this->promoCode->discount_value . 'TK'
            : $this->promoCode->discount_value . '%';

        $start = Carbon::parse($this->promoCode->start_date)->format('d F Y');
        $end = Carbon::parse($this->promoCode->end_date)->format('d F Y');

        $service->sendPromoCode($this->numbers, $code, $discountValue, $start, $end);
    }
}
