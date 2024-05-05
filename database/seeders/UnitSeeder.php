<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::create( [
            'id'=>1,
            'name'=>'Piece',
            'short_name'=>NULL,
            'status'=>'active',
            'business_id'=>1,
            'created_at'=>'2023-06-07 13:02:10',
            'updated_at'=>'2023-06-07 13:02:10'
        ] );



        Unit::create( [
            'id'=>3,
            'name'=>'Pcs',
            'short_name'=>NULL,
            'status'=>'active',
            'business_id'=>1,
            'created_at'=>'2023-11-17 17:30:27',
            'updated_at'=>'2023-11-17 20:31:24'
        ] );


    }
}
