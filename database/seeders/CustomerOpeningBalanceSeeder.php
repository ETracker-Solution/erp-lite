<?php

namespace Database\Seeders;

use App\Models\CustomerOpeningBalance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerOpeningBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CustomerOpeningBalance::truncate();
        CustomerOpeningBalance::factory(5)->create();
    }
}
