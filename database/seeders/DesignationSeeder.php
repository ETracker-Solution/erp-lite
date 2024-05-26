<?php

namespace Database\Seeders;

use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Developer',
                'department_id' => Department::all()->random()->id,
                'created_at' => Carbon::now(),
            ],
            [
                'name' => 'Accounts',
                'department_id' => Department::all()->random()->id,
                'created_at' => Carbon::now(),
            ],
        ];
        DB::table('designations')->insert($data);
    }
}
