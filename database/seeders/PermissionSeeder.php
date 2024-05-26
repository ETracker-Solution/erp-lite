<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $modules = ['purchase','store rm','production','data admin','system admin','account','store fg', 'sales','loyalty'];
        $suffix = ['admin','operator','viewer','approver'];

        foreach ($modules as $key => $module) {

            foreach ($suffix as $key => $item) {
                $permission['name'] = str_replace(' ','-',$module).'-'.$item;
                $permission['guard_name'] = 'web';
                $permission['display_name'] = ucwords($item);
                $permission['module_name'] = ucwords($module);
                $permission['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                $permission['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

                Permission::insert($permission);

            }
        }
    }
}
