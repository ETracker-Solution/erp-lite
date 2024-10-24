<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create( [
            'id'=>1,
            'name'=>'Samsung Galaxy A14 4G',
            'sku'=>'SKU-0001',
            'selling_price'=>400,
            'price'=>20.00,
            'photo_url'=>'/20230607010602eHkSHV.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-06-07 13:04:02',
            'updated_at'=>'2023-11-08 13:40:55'
        ] );

        Product::create( [
            'id'=>2,
            'name'=>'Samsung Galaxy A34 5G',
            'sku'=>'SKU-0002',
            'selling_price'=>400,
            'price'=>110.00,
            'photo_url'=>'/20230607010606aFC9Z2.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-06-07 13:05:06',
            'updated_at'=>'2023-11-08 13:40:40'
        ] );

        Product::create( [
            'id'=>3,
            'name'=>'Apple iPhone 14 Pro Max',
            'sku'=>'SKU-0003',
            'selling_price'=>400,
            'price'=>100.00,
            'photo_url'=>'/20230607010633YN696B.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-06-07 13:06:33',
            'updated_at'=>'2023-11-08 13:40:28'
        ] );

        Product::create( [
            'id'=>4,
            'name'=>'Apple iPhone 14 Plus',
            'sku'=>'SKU-0004',
            'selling_price'=>400,
            'price'=>90.00,
            'photo_url'=>'/20230607010613psq2VU.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-06-07 13:07:13',
            'updated_at'=>'2023-11-08 13:40:17'
        ] );

        Product::create( [
            'id'=>5,
            'name'=>'Nokia 105',
            'sku'=>'SKU-0005',
            'selling_price'=>400,
            'price'=>80.00,
            'photo_url'=>'/20230607010634u0U095.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-06-07 13:08:34',
            'updated_at'=>'2023-11-08 13:40:04'
        ] );

        Product::create( [
            'id'=>6,
            'name'=>'Nokia C2 2nd Edition',
            'sku'=>'SKU-0006',
            'selling_price'=>400,
            'price'=>70.00,
            'photo_url'=>'/20230607010605Up6nfR.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-06-07 13:09:05',
            'updated_at'=>'2023-11-08 13:39:50'
        ] );

        Product::create( [
            'id'=>7,
            'name'=>'Nokia 3.4',
            'sku'=>'SKU-0007',
            'selling_price'=>400,
            'price'=>70.00,
            'photo_url'=>'/20230607010639J37aQ6.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-06-07 13:09:39',
            'updated_at'=>'2023-11-08 13:39:36'
        ] );

        Product::create( [
            'id'=>8,
            'name'=>'Nokia 1.4',
            'sku'=>'SKU-0008',
            'selling_price'=>400,
            'price'=>60.00,
            'photo_url'=>'/20230607010628mehI8L.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-06-07 13:10:28',
            'updated_at'=>'2023-11-08 13:39:25'
        ] );

        Product::create( [
            'id'=>9,
            'name'=>'Testing2',
            'sku'=>'ABC',
            'selling_price'=>400,
            'price'=>50.00,
            'photo_url'=>'/20230618050627kEOyWf.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-06-18 17:51:28',
            'updated_at'=>'2023-11-08 13:39:11'
        ] );

        Product::create( [
            'id'=>10,
            'name'=>'headphone',
            'sku'=>'sku12',
            'selling_price'=>400,
            'price'=>400.00,
            'photo_url'=>'/20231108121110Q3rwMx.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-11-08 11:40:08',
            'updated_at'=>'2023-11-08 12:53:10'
        ] );

        Product::create( [
            'id'=>11,
            'name'=>'Lifeboy',
            'sku'=>'ETS-123',
            'selling_price'=>400,
            'price'=>20.00,
            'photo_url'=>'/20231108121115KzR33A.webp',
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>1,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-11-08 12:47:38',
            'updated_at'=>'2023-11-08 12:49:15'
        ] );

        Product::create( [
            'id'=>12,
            'name'=>'BD Tomato Sauce',
            'sku'=>'SKU123',
            'selling_price'=>400,
            'price'=>100.00,
            'photo_url'=>NULL,
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>10,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-11-17 17:34:01',
            'updated_at'=>'2023-11-17 20:39:10'
        ] );

        Product::create( [
            'id'=>13,
            'name'=>'BD Chilli Sauce',
            'sku'=>'SKU1234',
            'selling_price'=>400,
            'price'=>250.00,
            'photo_url'=>NULL,
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>10,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-11-17 17:35:15',
            'updated_at'=>'2023-11-17 20:35:41'
        ] );

        Product::create( [
            'id'=>14,
            'name'=>'BD Chanachur',
            'sku'=>'SKU1255',
            'selling_price'=>400,
            'price'=>90.00,
            'photo_url'=>NULL,
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>9,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-11-17 17:36:43',
            'updated_at'=>'2023-11-17 20:34:38'
        ] );

        Product::create( [
            'id'=>15,
            'name'=>'BD Butter Toast',
            'sku'=>'SKU12342',
            'selling_price'=>400,
            'price'=>60.00,
            'photo_url'=>NULL,
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>8,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-11-17 17:38:04',
            'updated_at'=>'2023-11-17 20:28:52'
        ] );

        Product::create( [
            'id'=>16,
            'name'=>'BD Potato Crackers',
            'sku'=>'sku5622',
            'selling_price'=>400,
            'price'=>10.00,
            'photo_url'=>NULL,
            'status'=>'active',
            'brand_id'=>1,
            'category_id'=>6,
            'business_id'=>1,
            'unit_id'=>1,
            'created_at'=>'2023-11-17 17:39:19',
            'updated_at'=>'2023-11-17 20:29:34'
        ] );
    }
}
