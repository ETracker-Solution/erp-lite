<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexes('inventory_transactions', [
            ['columns' => ['coi_id', 'store_id'], 'name' => 'inv_txn_coi_store_idx'],
            ['columns' => ['store_id', 'date'], 'name' => 'inv_txn_store_date_idx'],
            ['columns' => ['doc_type', 'doc_id'], 'name' => 'inv_txn_doc_idx'],
        ]);

        $this->addIndexes('sales', [
            ['columns' => ['date', 'outlet_id'], 'name' => 'sales_date_outlet_idx'],
            ['columns' => ['status', 'date'], 'name' => 'sales_status_date_idx'],
            ['columns' => ['created_at'], 'name' => 'sales_created_at_idx'],
        ]);

        $this->addIndexes('sale_items', [
            ['columns' => ['sale_id', 'product_id'], 'name' => 'sale_items_sale_product_idx'],
        ]);

        $this->addIndexes('account_transactions', [
            ['columns' => ['chart_of_account_id', 'date'], 'name' => 'acct_txn_coa_date_idx'],
            ['columns' => ['doc_type', 'doc_id'], 'name' => 'acct_txn_doc_idx'],
        ]);

        $this->addIndexes('sales_returns', [
            ['columns' => ['sale_id'], 'name' => 'sales_returns_sale_id_idx'],
        ]);
    }

    public function down(): void
    {
        $this->dropIndexes('inventory_transactions', [
            'inv_txn_coi_store_idx',
            'inv_txn_store_date_idx',
            'inv_txn_doc_idx',
        ]);
        $this->dropIndexes('sales', [
            'sales_date_outlet_idx',
            'sales_status_date_idx',
            'sales_created_at_idx',
        ]);
        $this->dropIndexes('sale_items', ['sale_items_sale_product_idx']);
        $this->dropIndexes('account_transactions', [
            'acct_txn_coa_date_idx',
            'acct_txn_doc_idx',
        ]);
        $this->dropIndexes('sales_returns', ['sales_returns_sale_id_idx']);
    }

    private function addIndexes(string $table, array $indexes): void
    {
        if (!Schema::hasTable($table)) {
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
        if (!Schema::hasTable($table)) {
            return;
        }

        $existingNames = array_column(Schema::getIndexes($table), 'name');
        foreach ($names as $name) {
            if (!in_array($name, $existingNames, true)) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) use ($name) {
                $blueprint->dropIndex($name);
            });
        }
    }
};
