<?php

namespace App\Jobs;

use App\Models\PromoCode;
use App\Models\SmsTemplate;
use App\Services\NovocomSmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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

//    public function handle(NovocomSmsService $service)
//    {
//        $code = $this->promoCode->code;
//        $discountValue = $this->promoCode->discount_type == 'fixed'
//            ? $this->promoCode->discount_value . 'TK'
//            : $this->promoCode->discount_value . '%';
//
//        $start = Carbon::parse($this->promoCode->start_date)->format('d F Y');
//        $end = Carbon::parse($this->promoCode->end_date)->format('d F Y');
//
//        $service->sendPromoCode($this->numbers, $code, $discountValue, $start, $end);
//    }

    public function handle(NovocomSmsService $gateway)
    {
        if (!$this->promoCode->sms_template_id) {
            Log::error('No SMS template configured for promo code', [
                'promo_code' => $this->promoCode->code
            ]);
            return;
        }

        $template = SmsTemplate::find($this->promoCode->sms_template_id);

        if (!$template || !$template->is_active) {
            Log::error('SMS template not found or inactive', [
                'template_id' => $this->promoCode->sms_template_id
            ]);
            return;
        }

        // Prepare variables in sequential order for replacement
        $variables = $this->prepareVariables();

        $text = $gateway->replaceVariables($template->message_template, $variables);

        $result = $gateway->sendSms2(
            $this->numbers,
            $text
        );

        if ($result['success']) {
            Log::info('Promo code SMS sent successfully', [
//                'phone' => $this->phoneNumber,
                'code' => $this->promoCode->code,
                'template' => $template->template_name
            ]);
        } else {
            Log::error('Failed to send promo code SMS', [
//                'phone' => $this->phoneNumber,
                'code' => $this->promoCode->code,
                'error' => $result['error']
            ]);

            // Re-throw to trigger retry
            throw new \Exception('SMS sending failed: ' . $result['error']);
        }
    }

    /**
     * Prepare variables based on template needs
     * Common pattern: [Code, Discount, Product/Category, Start Date, End Date]
     */
    protected function prepareVariables()
    {
        $variables = [];

        // Variable 1: Promo Code (always first)
        $variables[] = $this->promoCode->code;

        // Variable 2: Discount value with type
        if ($this->promoCode->discount_type === 'percentage') {
            $variables[] = $this->promoCode->discount_value . '%';
        } else {
            $variables[] = '৳' . number_format($this->promoCode->discount_value, 2);
        }

        // Variable 3: Product/Category name (if available)
        // You can customize this based on your business logic
//        $variables[] = $this->promoCode->product_name ?? 'All Products';

        // Variable 4: Start Date
        $variables[] = date('d-M-Y', strtotime($this->promoCode->start_date));

        // Variable 5: End Date
        $variables[] = date('d-M-Y', strtotime($this->promoCode->end_date));

        // Additional variables if needed
//        if ($this->customerName) {
//            // Some templates might need customer name
//            $variables[] = $this->customerName;
//        }

        return $variables;
    }
}
