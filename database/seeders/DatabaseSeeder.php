<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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
        $this->call(FactorySeeder::class);
        $this->call(StoreSeeder::class);
        $this->call(BatchSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(PermissionSeeder::class);

        $user = \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'employee_id' => 1,
            'password' => bcrypt('12345678'),
        ]);
        $user->syncPermissions(Permission::pluck('name'));
    }
}
