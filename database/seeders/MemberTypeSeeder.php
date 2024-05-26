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
                'name' => 'Member',
                'from_point' => 0,
                'to_point' => 999,
                'created_at' => $created_at,
            ],
            [
                'name' => 'Silver',
                'from_point' => 1000,
                'to_point' => 1999,
                'created_at' => $created_at,
            ],
            [
                'name' => 'Gold',
                'from_point' => 2000,
                'to_point' => 2999,
                'created_at' => $created_at,
            ],
            [
                'name' => 'Platinum',
                'from_point' => 3000,
                'to_point' => 3999,
                'created_at' => $created_at,
            ]

        ];

        DB::table('member_types')->insert($memberTypeArray);
    }
}
