<?php

namespace Database\Seeders;

use App\Models\SupplierGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SupplierGroup::create([
            'id' => 1,
            'name' => 'General',
            'code' => '10',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        SupplierGroup::create([
            'id' => 2,
            'name' => 'Raw Material',
            'code' => '1',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        SupplierGroup::create([
            'id' => 3,
            'name' => 'Packing Material',
            'code' => '2',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        SupplierGroup::create([
            'id' => 4,
            'name' => 'Brand Material',
            'code' => '6',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        SupplierGroup::create([
            'id' => 5,
            'name' => 'Engineering Material',
            'code' => '3',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);

        SupplierGroup::create([
            'id' => 6,
            'name' => 'Printing',
            'code' => '12',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        SupplierGroup::create([
            'id' => 7,
            'name' => 'C & F Agent',
            'code' => '9',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        SupplierGroup::create([
            'id' => 8,
            'name' => 'Lab Equipment',
            'code' => '4',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        SupplierGroup::create([
            'id' => 9,
            'name' => 'Import-Inventory',
            'code' => '7',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        SupplierGroup::create([
            'id' => 10,
            'name' => 'Insurance',
            'code' => '13',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);

    }
}
