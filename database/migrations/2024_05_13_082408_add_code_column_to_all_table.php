<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = $this->allTables();
        for ($i = 0; $i < count($tables); $i++) {
            if (Schema::hasColumn($tables[$i], 'uid')) {
                Schema::table($tables[$i], function (Blueprint $table) {
                    $table->dropColumn('uid');
                });
            }
            Schema::table($tables[$i], function (Blueprint $table) {
                $table->string('uid')->after('id')->nullable()->unique();
            });
//            \Illuminate\Support\Facades\DB::statement('UPDATE ' . $tables[$i] . ' SET uid = created_at');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = $this->allTables(); //DB::select('SHOW TABLES');
        for ($i = 0; $i < count($tables); $i++) {
            if (Schema::hasColumn($tables[$i], 'uid')) {
                Schema::table($tables[$i], function (Blueprint $table) {
                    $table->dropColumn('uid');
                });
            }
        }
    }

    public function allTables()
    {
        return [
            app(\App\Models\RawMaterialOpeningBalance::class)->getTable(),
            app(\App\Models\InventoryTransaction::class)->getTable(),
            app(\App\Models\Transaction::class)->getTable(),
            app(\App\Models\Purchase::class)->getTable(),
            app(\App\Models\Production::class)->getTable(),
        ];

    }
};
