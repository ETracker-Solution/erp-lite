<?php

namespace Database\Seeders;

use App\Models\Factory;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Factory::create([
            'id' => 1,
            'name' => 'Royal Village Factory',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);

        Store::create([
            'id' => 1,
            'name' => 'Store RM Head Office',
            'type' => 'RM',
            'doc_type' => 'ho',
        ]);
        Store::create([
            'id' => 2,
            'name' => 'Store RM Royal Village',
            'type' => 'RM',
            'doc_type' => 'factory',
            'doc_id' => 1,
        ]);
        Store::create([
            'id' => 3,
            'name' => 'Store FG Royal Village',
            'type' => 'FG',
            'doc_type' => 'factory',
            'doc_id' => 1,
        ]);
    }
}
