<?php
/**
 * Copyright (c) 2020. Electric
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Modules\Systems\Models\Entities\RoleHierarchy;

class ShopStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Create roles */
        $shopRole = Role::create(['guard_name' => 'shop', 'name' => 'shop_pushsale']);
        RoleHierarchy::create([
            'role_id' => $shopRole->id,
            'hierarchy' => 10,
        ]);
    }
}
