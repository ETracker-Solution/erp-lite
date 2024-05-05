<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create( [
            'id'=>1,
            'name'=>'Sujon',
            'mobile'=>"01710355700",
            'email'=>'customer@gmail.com',
            'address'=>'Dhaka',
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );
    }
}
