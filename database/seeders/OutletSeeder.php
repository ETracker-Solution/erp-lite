<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\Outlet;
use App\Models\OutletTransactionConfig;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('outlets')->insert([
            [
                'id' => 1,
                'name' => 'Katherpol',
                'address' => 'katherpol Gandaria',
            ],
            [
                'id' => 2,
                'name' => 'Maniknagar',
                'address' => 'Maniknagar Bishawroad',
            ],
            [
                'id' => 3,
                'name' => 'Tikatuli',
                'address' => 'Tikatoli-Gopibag',
            ],
            [
                'id' => 4,
                'name' => 'Mugda',
                'address' => 'Mugda bishawroad',
            ],
            [
                'id' => 5,
                'name' => 'Ray Shahab Bazar',
                'address' => 'Ray Shahab Bazar-Jonshon Road',
            ],
            [
                'id' => 6,
                'name' => 'Baily Road',
                'address' => 'Baily Road-ShantiNagarmor',
            ],
            [
                'id' => 7,
                'name' => 'Basaboo',
                'address' => 'Basaboo-Balur Math',
            ],
            [
                'id' => 8,
                'name' => 'Jatrabari',
                'address' => 'Jatrabari-shohaid Faruk Road',
            ],
            [
                'id' => 9,
                'name' => 'Nazim Uddin Road',
                'address' => 'Nazim Uddin Road-Shankharpol',
            ],
            [
                'id' => 10,
                'name' => 'Ialbag',
                'address' => 'Ialbag-Dakarshori Mandir',
            ],
            [
                'id' => 11,
                'name' => 'Shipaibag',
                'address' => 'Shipaibag-Goran bazar',
            ],
            [
                'id' => 12,
                'name' => 'Malibag',
                'address' => 'Malibag-Chawdhuri para Dit Road',
            ],
            [
                'id' => 13,
                'name' => 'Nazira Bazar',
                'address' => 'New',
            ],
            [
                'id' => 14,
                'name' => 'Jurain',
                'address' => 'New',
            ],
        ]);

        $outlets = Outlet::all();
        foreach ($outlets as $outlet) {
            Store::create([
                'name' => 'Store FG ' . $outlet->name,
                'type' => 'FG',
                'doc_type' => 'outlet',
                'doc_id' => $outlet->id,
            ]);

            $methods = ['Cash', 'Bkash'];
            foreach ($methods as $method) {
                $exists = ChartOfAccount::where('name', $method)->first();
                if (!$exists) {
                    $exists = ChartOfAccount::create([
                        'name' => $method,
                        'type' => 'group',
                        'account_type' => 'debit',
                        'root_account_type' => 'as',
                        'parent_id' => 5
                    ]);
                }
                $account = $exists->subChartOfAccounts()->create([
                    'name' => $method . ' ' . $outlet->name,
                    'type' => 'ledger',
                    'account_type' => 'debit',
                    'root_account_type' => 'as',
                ]);

                OutletTransactionConfig::create([
                    'outlet_id' => $outlet->id,
                    'coa_id' => $account->id,
                    'type' => $method
                ]);
            }
        }
    }
}
