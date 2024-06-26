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
            'name'=>'Amin Supplier',
            'mobile'=>"01710355789",
            'email'=>'supplier@gmail.com',
            'address'=>'Dhaka',
            'supplier_group_id'=>1,
            'status'=>'active',
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );
        Supplier::create( [
            'id'=>2,
            'name'=>'Jamil Supplier',
            'mobile'=>"01710355709",
            'email'=>'supplier2@gmail.com',
            'address'=>'Dhaka',
            'supplier_group_id'=>2,
            'status'=>'active',
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );
        Supplier::create( [
            'id'=>3,
            'name'=>'Rafi Supplier',
            'mobile'=>"01710355709",
            'email'=>'supplier2@gmail.com',
            'address'=>'Dhaka',
            'supplier_group_id'=>2,
            'status'=>'active',
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );
    }
}
