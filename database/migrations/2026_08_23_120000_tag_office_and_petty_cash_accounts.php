<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * FT voucher "Transfer To" requires default_type = office_account.
     * Seed data never set that tag — fix HO cash/bank ledgers in place.
     */
    public function up(): void
    {
        DB::table('chart_of_accounts')
            ->where('type', 'ledger')
            ->where('is_bank_cash', 'yes')
            ->where(function ($q) {
                $q->where('name', 'Cash in hand')
                    ->orWhere('name', 'like', 'Office %')
                    ->orWhere('name', 'like', '% Office Account%');
            })
            ->where(function ($q) {
                $q->whereNull('default_type')
                    ->orWhere('default_type', '');
            })
            ->update(['default_type' => 'office_account']);

        DB::table('chart_of_accounts')
            ->where('type', 'ledger')
            ->where(function ($q) {
                $q->where('name', 'Petty Cash')
                    ->orWhere('name', 'like', '%- Petty Cash');
            })
            ->where(function ($q) {
                $q->whereNull('default_type')
                    ->orWhere('default_type', '');
            })
            ->update(['default_type' => 'petty_cash']);
    }

    public function down(): void
    {
        DB::table('chart_of_accounts')
            ->where('default_type', 'office_account')
            ->where(function ($q) {
                $q->where('name', 'Cash in hand')
                    ->orWhere('name', 'like', 'Office %')
                    ->orWhere('name', 'like', '% Office Account%');
            })
            ->update(['default_type' => null]);

        DB::table('chart_of_accounts')
            ->where('default_type', 'petty_cash')
            ->where(function ($q) {
                $q->where('name', 'Petty Cash')
                    ->orWhere('name', 'like', '%- Petty Cash');
            })
            ->update(['default_type' => null]);
    }
};
