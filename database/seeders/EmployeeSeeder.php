<?php

namespace Database\Seeders;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Employee::create( [
            'id'=>1,
            'employee_id'=>101,
            'name'=>'Sohel Rana',
            'phone'=>"01713616087",
            'email'=>'sohel@gmail.com',
            'present_address'=>'Khilket,Dhaka',
            'created_at'=> Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'=> Carbon::now()->format('Y-m-d H:i:s'),
        ] );
        Employee::create( [
            'id'=>2,
            'employee_id'=>102,
            'name'=>'Sabbir Rana',
            'phone'=>"01713616443",
            'email'=>'sabbir@gmail.com',
            'present_address'=>'Mirpur,Dhaka',
            'created_at'=> Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'=> Carbon::now()->format('Y-m-d H:i:s'),
        ] );
    }
}
