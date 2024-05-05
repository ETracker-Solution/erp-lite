<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create( [
            'id'=>1,
            'name'=>'Hasan',
            'mobile'=>"01710355789",
            'email'=>'supplier@gmail.com',
            'address'=>'Dhaka',
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );
    }
}
