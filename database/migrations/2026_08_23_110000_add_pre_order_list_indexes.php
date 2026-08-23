<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexes('pre_orders', [
            ['columns' => ['delivery_point_id', 'status'], 'name' => 'pre_orders_delivery_status_idx'],
            ['columns' => ['delivery_date'], 'name' => 'pre_orders_delivery_date_idx'],
            ['columns' => ['order_date'], 'name' => 'pre_orders_order_date_idx'],
            ['columns' => ['order_number'], 'name' => 'pre_orders_order_number_idx'],
        ]);

        $this->addIndexes('others_outlet_sales', [
            ['columns' => ['invoice_number'], 'name' => 'oos_invoice_number_idx'],
        ]);
    }

    public function down(): void
    {
        $this->dropIndexes('pre_orders', [
            'pre_orders_delivery_status_idx',
            'pre_orders_delivery_date_idx',
            'pre_orders_order_date_idx',
            'pre_orders_order_number_idx',
        ]);
        $this->dropIndexes('others_outlet_sales', ['oos_invoice_number_idx']);
    }

    private function addIndexes(string $table, array $indexes): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $existingNames = array_column(Schema::getIndexes($table), 'name');
        foreach ($indexes as $index) {
            if (in_array($index['name'], $existingNames, true)) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) use ($index) {
                $blueprint->index($index['columns'], $index['name']);
            });
        }
    }

    private function dropIndexes(string $table, array $names): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $existingNames = array_column(Schema::getIndexes($table), 'name');
        foreach ($names as $name) {
            if (! in_array($name, $existingNames, true)) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) use ($name) {
                $blueprint->dropIndex($name);
            });
        }
    }
};
