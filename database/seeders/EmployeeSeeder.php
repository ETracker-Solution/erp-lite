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
            'employee_id'=>100,
            'name' => 'Super Admin',
            'phone'=>"01713616087",
            'email' => 'admin@gmail.com',
            'present_address'=>'Khilket,Dhaka',
            'created_at'=> Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'=> Carbon::now()->format('Y-m-d H:i:s'),
            'user_of'=>'ho',
        ] );
        Employee::create( [
            'id'=>2,
            'employee_id'=>101,
            'name'=>'Factory User',
            'phone'=>"01713616087",
            'email'=>'factory@gmail.com',
            'present_address'=>'Khilket,Dhaka',
            'created_at'=> Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'=> Carbon::now()->format('Y-m-d H:i:s'),
            'user_of'=>'factory',
            'factory_id'=>1
        ] );
        Employee::create( [
            'id'=>3,
            'employee_id'=>102,
            'name'=>'Outlet User',
            'phone'=>"01713616443",
            'email'=>'outlet@gmail.com',
            'present_address'=>'Mirpur,Dhaka',
            'created_at'=> Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at'=> Carbon::now()->format('Y-m-d H:i:s'),
            'user_of'=>'outlet',
            'outlet_id'=>1
        ] );
    }
}
