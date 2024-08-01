<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_at = now();
        $memberTypeArray = [
            [
                'name' => 'New Customer',
                'from_point' => 0,
                'to_point' => 499,
                'minimum_purchase' => 100,
                'discount' => 00,
                'created_at' => $created_at,
            ],
            [
                'name' => 'Silver Member',
                'from_point' => 500,
                'to_point' => 999,
                'minimum_purchase' => 149,
                'discount' => 10,
                'created_at' => $created_at,
            ],
            [
                'name' => 'Gold Member',
                'from_point' => 1000,
                'to_point' => 1999,
                'minimum_purchase' => 199,
                'discount' => 15,
                'created_at' => $created_at,
            ],
            [
                'name' => 'Platinum Member',
                'from_point' => 2000,
                'to_point' => 2999,
                'minimum_purchase' => 249,
                'discount' => 20,
                'created_at' => $created_at,
            ]

        ];

        DB::table('member_types')->insert($memberTypeArray);
    }
}
