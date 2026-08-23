<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Historical FTVs were saved with blank uid; use id as the voucher number.
        DB::statement('UPDATE fund_transfer_vouchers SET uid = id WHERE uid IS NULL OR uid = ""');
    }

    public function down(): void
    {
        // Non-reversible data backfill.
    }
};
