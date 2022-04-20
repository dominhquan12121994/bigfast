<?php

namespace App\Modules\Systems\Services;

use Spatie\Permission\Models\Role;

class RolesService{

//    static $defaultRoles = ['guest'];

    public static function get(){
//        $roles = Role::where('guard_name', 'admin')->get();
        $roles = Role::get();
        $result = array();
        foreach($roles as $role){
            array_push($result, $role->name);
        }
//        return array_merge(self::$defaultRoles, $result);
        return $result;
    }

}