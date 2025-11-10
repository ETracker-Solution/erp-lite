<?php

namespace App\Models;

use App\Services\NovocomSmsService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function getActiveTemplates()
    {
        return self::where('is_active', true)
            ->orderBy('template_name')
            ->get();
    }

    /**
     * Check if template has dynamic variables
     */
    public function hasDynamicVariables()
    {
        return str_contains($this->message_template, '{#var#}');
    }

    /**
     * Extract variable placeholders from template
     */
    public function getVariablePlaceholders()
    {
        preg_match_all('/\{#[^}]+#\}/', $this->message_template, $matches);
        return $matches[0] ?? [];
    }

    public function getVariableLabels()
    {
        $count = $this->getVariableCount();

        // Common patterns based on variable count
        $patterns = [
            1 => ['Promo Code'],
            2 => ['Promo Code', 'Discount Amount'],
            3 => ['Promo Code', 'Discount Amount', 'Product/Category'],
            4 => ['Promo Code', 'Discount Amount', 'Start Date', 'End Date'],
            5 => ['Promo Code', 'Discount Amount', 'Product/Category', 'Start Date', 'End Date']
        ];

        return $patterns[$count] ?? array_fill(0, $count, 'Variable');
    }

    public function getVariableCount()
    {
        return NovocomSmsService::countVariables($this->message_template);
    }
}
