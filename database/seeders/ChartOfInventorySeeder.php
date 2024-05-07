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
            'type'=>'fixed',
            'rootAccountType'=>'RM',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>2,
            'name'=>'Work In Process',
            'type'=>'fixed',
            'rootAccountType'=>'WIP',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>3,
            'name'=>'Finish Goods',
            'type'=>'fixed',
            'rootAccountType'=>'FG',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>4,
            'name'=>'By Product',
            'type'=>'fixed',
            'rootAccountType'=>'BP',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>5,
            'parent_id'=>1,
            'name'=>'Prime Raw Material',
            'type'=>'group',
            'rootAccountType'=>'RM',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>6,
            'parent_id'=>5,
            'name'=>'Flour',
            'type'=>'item',
            'rootAccountType'=>'RM',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>7,
            'parent_id'=>5,
            'name'=>'Color Rice',
            'type'=>'item',
            'rootAccountType'=>'RM',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>8,
            'parent_id'=>5,
            'name'=>'Chicken',
            'type'=>'item',
            'rootAccountType'=>'RM',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>9,
            'parent_id'=>5,
            'name'=>'Powder Milk',
            'type'=>'item',
            'rootAccountType'=>'RM',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>10,
            'parent_id'=>1,
            'name'=>'Packing Raw Material',
            'type'=>'group',
            'rootAccountType'=>'RM',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>11,
            'parent_id'=>10,
            'name'=>'Pastry Raper poly',
            'type'=>'item',
            'rootAccountType'=>'RM',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
        ChartOfInventory::create( [
            'id'=>12,
            'parent_id'=>10,
            'name'=>'A4 Paper',
            'type'=>'item',
            'rootAccountType'=>'RM',
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );
    }
}
