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
        SupplierGroup::create( [
            'id'=>1,
            'name'=>'General',
            'code'=>'001',
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );
        SupplierGroup::create( [
            'id'=>2,
            'name'=>'Raw Material',
            'code'=>'002',
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );
        SupplierGroup::create( [
            'id'=>3,
            'name'=>'Packing Material',
            'code'=>'001',
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );
    }
}
