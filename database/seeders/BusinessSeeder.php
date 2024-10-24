<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Business::create( [
            'id'=>1,
            'name'=>'E Gallery',
            'address'=>'Dhaka',
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );
    }
}
