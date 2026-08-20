<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexes('sale_items', [
            ['columns' => ['product_id'], 'name' => 'sale_items_product_id_idx'],
        ]);

        $this->addIndexes('sales', [
            ['columns' => ['customer_id', 'date'], 'name' => 'sales_customer_date_idx'],
        ]);

        $this->addIndexes('inventory_transactions', [
            ['columns' => ['coi_id', 'date'], 'name' => 'inv_txn_coi_date_idx'],
        ]);

        $this->addIndexes('requisitions', [
            ['columns' => ['to_factory_id', 'type', 'status', 'delivery_status'], 'name' => 'req_factory_type_status_idx'],
            ['columns' => ['outlet_id', 'type', 'status'], 'name' => 'req_outlet_type_status_idx'],
        ]);

        $this->addIndexes('requisition_items', [
            ['columns' => ['requisition_id', 'coi_id'], 'name' => 'req_items_req_coi_idx'],
        ]);

        $this->addIndexes('requisition_deliveries', [
            ['columns' => ['type', 'status', 'from_store_id'], 'name' => 'req_del_type_status_store_idx'],
            ['columns' => ['requisition_id', 'status'], 'name' => 'req_del_req_status_idx'],
        ]);

        $this->addIndexes('requisition_delivery_items', [
            ['columns' => ['coi_id'], 'name' => 'req_del_items_coi_idx'],
        ]);

        $this->addIndexes('others_outlet_sales', [
            ['columns' => ['delivery_point_id', 'status'], 'name' => 'oos_delivery_status_idx'],
            ['columns' => ['outlet_id', 'date'], 'name' => 'oos_outlet_date_idx'],
        ]);

        $this->addIndexes('pre_order_items', [
            ['columns' => ['coi_id'], 'name' => 'pre_order_items_coi_idx'],
        ]);

        $this->addIndexes('inventory_transfer_items', [
            ['columns' => ['coi_id'], 'name' => 'inv_transfer_items_coi_idx'],
        ]);

        $this->addIndexes('chart_of_inventories', [
            ['columns' => ['parent_id', 'type', 'rootAccountType', 'status'], 'name' => 'coi_parent_type_root_status_idx'],
        ]);

        $this->addIndexes('inventory_adjustments', [
            ['columns' => ['store_id', 'transaction_type', 'date'], 'name' => 'inv_adj_store_type_date_idx'],
        ]);

        $this->addIndexes('expenses', [
            ['columns' => ['created_at'], 'name' => 'expenses_created_at_idx'],
        ]);

        $this->addIndexes('purchases', [
            ['columns' => ['created_at'], 'name' => 'purchases_created_at_idx'],
        ]);

        $this->addIndexes('customers', [
            ['columns' => ['type', 'status'], 'name' => 'customers_type_status_idx'],
        ]);
    }

    public function down(): void
    {
        $this->dropIndexes('sale_items', ['sale_items_product_id_idx']);
        $this->dropIndexes('sales', ['sales_customer_date_idx']);
        $this->dropIndexes('inventory_transactions', ['inv_txn_coi_date_idx']);
        $this->dropIndexes('requisitions', ['req_factory_type_status_idx', 'req_outlet_type_status_idx']);
        $this->dropIndexes('requisition_items', ['req_items_req_coi_idx']);
        $this->dropIndexes('requisition_deliveries', ['req_del_type_status_store_idx', 'req_del_req_status_idx']);
        $this->dropIndexes('requisition_delivery_items', ['req_del_items_coi_idx']);
        $this->dropIndexes('others_outlet_sales', ['oos_delivery_status_idx', 'oos_outlet_date_idx']);
        $this->dropIndexes('pre_order_items', ['pre_order_items_coi_idx']);
        $this->dropIndexes('inventory_transfer_items', ['inv_transfer_items_coi_idx']);
        $this->dropIndexes('chart_of_inventories', ['coi_parent_type_root_status_idx']);
        $this->dropIndexes('inventory_adjustments', ['inv_adj_store_type_date_idx']);
        $this->dropIndexes('expenses', ['expenses_created_at_idx']);
        $this->dropIndexes('purchases', ['purchases_created_at_idx']);
        $this->dropIndexes('customers', ['customers_type_status_idx']);
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
