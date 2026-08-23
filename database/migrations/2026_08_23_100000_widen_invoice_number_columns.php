<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Outlet names like "CT-Bashundhara-" plus ym plus sequence exceed the old VARCHAR(20).
        // MySQL then truncated, so many invoices stored the same number.
        $this->widen('sales', 'invoice_number');
        $this->widen('others_outlet_sales', 'invoice_number');
        $this->widen('pre_orders', 'order_number');
    }

    public function down(): void
    {
        $this->widen('sales', 'invoice_number', 20);
        $this->widen('others_outlet_sales', 'invoice_number', 20);
        $this->widen('pre_orders', 'order_number', 20);
    }

    private function widen(string $table, string $column, int $length = 64): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $quotedTable = str_replace('`', '``', $table);
        $quotedColumn = str_replace('`', '``', $column);
        $length = (int) $length;

        DB::statement("ALTER TABLE `{$quotedTable}` MODIFY `{$quotedColumn}` VARCHAR({$length}) NOT NULL");
    }
};
