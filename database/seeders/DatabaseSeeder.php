<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $this->call(BusinessSeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(AttributeSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(ChartOfInventorySeeder::class);
        $this->call(ChartOfAccountSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(SupplierGroupSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(StoreSeeder::class);
        $this->call(BatchSeeder::class);
        $this->call(EmployeeSeeder::class);

        \App\Models\User::factory()->create([
            'name' => 'Mohammad Ali',
            'email' => 'admin@gmail.com',
            'employee_id' => 1,
            'password' => bcrypt('12345678'),
        ]);
    }
}
