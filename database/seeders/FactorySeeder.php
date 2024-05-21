<?php

namespace Database\Seeders;

use App\Models\Factory;
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
            'name' => 'Gazipur Factory',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
        Factory::create([
            'id' => 2,
            'name' => 'Manikganj Factory',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
    }
}
