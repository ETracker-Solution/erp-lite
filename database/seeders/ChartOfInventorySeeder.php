<?php

namespace Database\Seeders;

use App\Models\ChartOfInventory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartOfInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ChartOfInventory::create( [
            'id'=>1,
            'name'=>'Raw Material',
            'type'=>'RM',
            'rootAccountType'=>'',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>2,
            'name'=>'Work In Process',
            'type'=>'WIP',
            'rootAccountType'=>'',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>3,
            'name'=>'Finish Goods',
            'type'=>'FG',
            'rootAccountType'=>'',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>4,
            'name'=>'By Product',
            'type'=>'FG',
            'rootAccountType'=>'',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
    }
}
