<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mark existing bank/cash payment groups so outletAccountTypeOptions()
     * can be read from chart_of_accounts instead of a hardcoded PHP list.
     */
    public function up(): void
    {
        $names = [
            'Cash', 'Bkash', 'Nagad', 'Bank', 'Rocket', 'Upay',
            'DBBL', 'UCB', 'Nexus', 'PBL', 'Due', 'FoodPanda',
            'CityBank', 'PBLQR', 'FOODIE',
        ];

        DB::table('chart_of_accounts')
            ->where('type', 'group')
            ->whereIn('name', $names)
            ->where(function ($q) {
                $q->whereNull('default_type')
                    ->orWhere('default_type', '');
            })
            ->update([
                'default_type' => 'payment_method',
                'is_bank_cash' => 'yes',
            ]);
    }

    public function down(): void
    {
        DB::table('chart_of_accounts')
            ->where('type', 'group')
            ->where('default_type', 'payment_method')
            ->update(['default_type' => null]);
    }
};
