<?php
/**
 * Copyright (c) 2021. Electric
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\StringHelper;
use App\Modules\Systems\Constants\PermissionConstant;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrRoles = array('admin', 'accountancy', 'coordinator', 'pushsale', 'support', 'shipper', 'pickup', 'refund');
        $permissions = PermissionConstant::permissions;

//        unset($arrRoles[0]);
        foreach ($arrRoles as $roleName) {
            $role = Role::where('name' , '=' , $roleName)->first();
            foreach ($permissions as $module => $permission) {
                unset($permission['name']);
                foreach($permission as $action => $actionItem) {
                    if (in_array($roleName, $actionItem['default'])) {
                        $name = 'action_' . $module . '_' . $action;
                        $per = Permission::where('name', '=', StringHelper::vn_to_str($name))->get();
                        if(count($per) === 0){
                            $per = Permission::create(['name' => StringHelper::vn_to_str($name)]);
                        }
                        $role->givePermissionTo($per);
                    }
                }
            }
        }
    }
}