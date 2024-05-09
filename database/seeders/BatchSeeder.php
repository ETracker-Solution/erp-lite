<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Batch::create([
            'id' => 1,
            'batch_no' => '20-OCT-HABIB',
            'p_manager' => 'M Habib',
            'date' => date('Y-m-d'),
            'description' => 'description Unique',
            'created_at' => '2023-06-07 13:01:13',
            'updated_at' => '2023-06-07 13:01:13'
        ]);
    }
}
