<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create( [
            'id'=>1,
            'parent_id'=>NULL,
            'name'=>'Electronics',
            'slug'=>'electronics',
            'status'=>'active',
            'business_id'=>1,
            'created_at'=>'2023-06-07 13:01:13',
            'updated_at'=>'2023-06-07 13:01:13'
        ] );



        Category::create( [
            'id'=>4,
            'parent_id'=>1,
            'name'=>'airpod',
            'slug'=>'airpod',
            'status'=>'active',
            'business_id'=>1,
            'created_at'=>'2023-11-08 11:37:18',
            'updated_at'=>'2023-11-08 11:37:18'
        ] );



        Category::create( [
            'id'=>5,
            'parent_id'=>NULL,
            'name'=>'Snacks',
            'slug'=>'snacks',
            'status'=>'active',
            'business_id'=>1,
            'created_at'=>'2023-11-17 17:22:32',
            'updated_at'=>'2023-11-17 17:22:32'
        ] );



        Category::create( [
            'id'=>6,
            'parent_id'=>5,
            'name'=>'chips',
            'slug'=>'chips',
            'status'=>'active',
            'business_id'=>1,
            'created_at'=>'2023-11-17 17:22:44',
            'updated_at'=>'2023-11-17 17:22:44'
        ] );



        Category::create( [
            'id'=>7,
            'parent_id'=>5,
            'name'=>'cream biscuits',
            'slug'=>'cream-biscuits',
            'status'=>'active',
            'business_id'=>1,
            'created_at'=>'2023-11-17 17:23:33',
            'updated_at'=>'2023-11-17 17:23:33'
        ] );



        Category::create( [
            'id'=>8,
            'parent_id'=>5,
            'name'=>'Toast',
            'slug'=>'toast',
            'status'=>'active',
            'business_id'=>1,
            'created_at'=>'2023-11-17 17:24:10',
            'updated_at'=>'2023-11-17 17:24:10'
        ] );



        Category::create( [
            'id'=>9,
            'parent_id'=>5,
            'name'=>'Chanachur',
            'slug'=>'chanachur',
            'status'=>'active',
            'business_id'=>1,
            'created_at'=>'2023-11-17 20:32:31',
            'updated_at'=>'2023-11-17 20:32:31'
        ] );



        Category::create( [
            'id'=>10,
            'parent_id'=>5,
            'name'=>'Sauce',
            'slug'=>'sauce',
            'status'=>'active',
            'business_id'=>1,
            'created_at'=>'2023-11-17 20:33:28',
            'updated_at'=>'2023-11-17 20:33:28'
        ] );
    }
}
