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
                'from_amount' => 50,
                'to_amount' => 200,
                'per_amount' => 100,
                'point' => '50',
                'member_type_id' => '1',
                'created_at' => $created_at,
            ],
            [
                'from_amount' => 50,
                'to_amount' => 200,
                'per_amount' => 100,
                'point' => '60',
                'member_type_id' => '2',
                'created_at' => $created_at,
            ],
            [
                'from_amount' => 50,
                'to_amount' => 200,
                'per_amount' => 100,
                'point' => '80',
                'member_type_id' => '3',
                'created_at' => $created_at,
            ],
            [
                'from_amount' => 100,
                'to_amount' => 200,
                'per_amount' => 100,
                'point' => '100',
                'member_type_id' => '4',
                'created_at' => $created_at,
            ],
        ];

        DB::table('member_points')->insert($memberPointArray);
    }
}
