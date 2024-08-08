<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_at = now();
        $memberPointArray =[
            [
                'from_amount' => 0,
                'to_amount' => 0,
                'per_amount' => 100,
                'point' => '1',
                'member_type_id' => '1',
                'created_at' => $created_at,
            ],
            [
                'from_amount' => 0,
                'to_amount' => 0,
                'per_amount' => 100,
                'point' => '1',
                'member_type_id' => '2',
                'created_at' => $created_at,
            ],
            [
                'from_amount' => 0,
                'to_amount' => 0,
                'per_amount' => 100,
                'point' => '1',
                'member_type_id' => '3',
                'created_at' => $created_at,
            ],
            [
                'from_amount' => 0,
                'to_amount' => 0,
                'per_amount' => 100,
                'point' => '1',
                'member_type_id' => '4',
                'created_at' => $created_at,
            ],
        ];

        DB::table('member_points')->insert($memberPointArray);
    }
}
