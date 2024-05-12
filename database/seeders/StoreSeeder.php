<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::create([
            'id' => 1,
            'name' => 'RM Gazipur Factory',
            'type' => 'RM',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        Store::create([
            'id' => 2,
            'name' => 'RM Savar Factory',
            'type' => 'RM',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        Store::create([
            'id' => 3,
            'name' => 'RM Gabtoli Factory',
            'type' => 'RM',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        Store::create([
            'id' => 4,
            'name' => 'Maniknagor FG',
            'type' => 'FG',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        Store::create([
            'id' => 5,
            'name' => 'Mirpur FG',
            'type' => 'FG',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
    }
}
