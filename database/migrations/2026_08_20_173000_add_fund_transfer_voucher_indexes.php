<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexes('fund_transfer_vouchers', [
            ['columns' => ['date'], 'name' => 'ftv_date_idx'],
            ['columns' => ['status', 'date'], 'name' => 'ftv_status_date_idx'],
            ['columns' => ['credit_account_id', 'status'], 'name' => 'ftv_credit_status_idx'],
            ['columns' => ['debit_account_id'], 'name' => 'ftv_debit_account_idx'],
        ]);

        $this->addIndexes('outlet_accounts', [
            ['columns' => ['outlet_id', 'coa_id'], 'name' => 'outlet_accounts_outlet_coa_idx'],
        ]);
    }

    public function down(): void
    {
        $this->dropIndexes('fund_transfer_vouchers', [
            'ftv_date_idx',
            'ftv_status_date_idx',
            'ftv_credit_status_idx',
            'ftv_debit_account_idx',
        ]);
        $this->dropIndexes('outlet_accounts', ['outlet_accounts_outlet_coa_idx']);
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
