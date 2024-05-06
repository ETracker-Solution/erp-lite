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
            'type'=>'group',
            'rootAccountType'=>'RM',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>2,
            'name'=>'Work In Process',
            'type'=>'group',
            'rootAccountType'=>'WIP',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>3,
            'name'=>'Finish Goods',
            'type'=>'group',
            'rootAccountType'=>'FG',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>4,
            'name'=>'By Product',
            'type'=>'item',
            'rootAccountType'=>'FG',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
    }
}