<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//      DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//      Permission::truncate();
//      DB::table('model_has_permissions')->truncate();

        // $modules = ['purchase','store rm','production','data admin','system admin','account','store fg', 'sales','loyalty'];
        // $suffix = ['admin','operator','viewer','approver'];

        $newModule = [
        'accounts'=>[
            'receive voucher','payment voucher','journal voucher','ft voucher','supplier voucher','leger report','financial report'
        ],
        'purchase'=>[
            'goods purchase bill','purchase return bill'
        ],
        'store rm'=>[
            'rm inventory transfer','rm inventory adjustment','create rm requisition','rm requisition delivery','rm inventory report'
        ],
        'production'=>[
            'batch entry','rm consumption','fg production'
        ],
        'store fg'=>[
            'fg inventory transfer','fg inventory adjustment','create fg requisition','fg requisition list','fg requisition delivery','fg delivery receive','fg inventory report'
        ],
        'sales'=>[
            'pre orders list','pre order entry','sales','sales report'
        ],
        'loyalty'=>[
            'earn point','redeem point','point setting','membership','membertype','promo code'
        ],
        'data admin'=>[
            'chart of accounts','inventory item list','unit list','store list','supplier group list','supplier list','gl account','raw metarials','finish goods','customer ob','supplier ob','create factory','create outlet','designation','department'
        ],
        'system admin'=>[
            'user list','employees','outlet payment','system setting'
        ]
    ];

        foreach ($newModule as $module => $suffix) {

            foreach ($suffix as $key => $item) {
                $permission['name'] = str_replace(' ','-',$module).'-'.str_replace(' ','-',$item);
                $permission['guard_name'] = 'web';
                $permission['display_name'] = ucwords($item);
                $permission['module_name'] = ucwords($module);
                $permission['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                $permission['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');

                Permission::insert($permission);

            }
        }
//      $user = User::where('is_super',true)->first();
//      $user->syncPermissions(Permission::pluck('name'));
//      DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

}
