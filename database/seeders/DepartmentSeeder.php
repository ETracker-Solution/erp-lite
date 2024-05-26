<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'HR',
                'created_at' => Carbon::now(),

            ],
            [
                'name' => 'It',
                'created_at' => Carbon::now(),
            ],
        ];
        DB::table('departments')->insert($data);
    }
}
