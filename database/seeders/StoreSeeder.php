<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::create( [
            'id'=>1,
            'name'=>'Gazipur Factory',
            'type'=>'RM',
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );
    }
}
