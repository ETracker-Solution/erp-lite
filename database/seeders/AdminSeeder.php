<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Admin::create([
            'name' => 'Super Admin',
            'email' => 'super@gmail.com',
            'password' => bcrypt('12345678'),
            'created_at'=> now()->format('Y-m-d H:i:s'),
            'updated_at'=>now()->format('Y-m-d H:i:s'),
        ]);
    }
}
