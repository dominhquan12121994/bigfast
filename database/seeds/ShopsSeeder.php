<?php
/**
 * Copyright (c) 2020. Electric
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Orders\Models\Entities\OrderReceiver;
use App\Modules\Orders\Models\Entities\OrderShopBank;
use App\Modules\Orders\Models\Entities\OrderShopAddress;
use App\Modules\Systems\Models\Entities\RoleHierarchy;

class ShopsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfUsers = 0;
        $faker = Faker::create();
        /* Create roles */
        $shopRole = Role::create(['guard_name' => 'shop', 'name' => 'shop']);
        RoleHierarchy::create([
            'role_id' => $shopRole->id,
            'hierarchy' => 9,
        ]);

        /*  insert shops   */
        $phone = '0987654321';
        $email = 'huydien.it@gmail.com';
        $token = md5(config('app.name').'-'.$phone.'-'.$email.'-'.date('Y'));
        $token = substr_replace($token, '-', 6, 0);
        $token = substr_replace($token, '-', 13, 0);
        $token = substr_replace($token, '-', 21, 0);
        $token = substr_replace($token, '-', 29, 0);
        $shop = OrderShop::create([
            'name' => 'electric',
            'phone' => $phone,
            'email' => $email,
            'email_verified_at' => now(),
            'address' => $faker->address(),
            'password' => 'password', // password
            'remember_token' => Str::random(10),
            'menuroles' => 'shop',
            'api_token' => $token
        ]);
        $shop->assignRole($shopRole);
        OrderShopBank::create([
            'id' => $shop->id,
            'bank_name' => Str::random(10),
            'bank_branch' => Str::random(20),
            'stk_name' => Str::random(50),
            'stk' => Str::random(15),
            'cycle_cod' => 'friday',
            'cycle_cod_day' => 7,
            'date_reconcile' => date('Y-m-d', strtotime('-31 day'))
        ]);
        OrderShopAddress::create([
            'shop_id' => $shop->id,
            'p_id' => 1,
            'd_id' => 1,
            'w_id' => 1,
            'type' => 'send',
            'name' => $faker->name(),
            'phone' => $faker->tollFreePhoneNumber(),
            'address' => $faker->address(),
        ]);

        for($i = 0; $i<$numberOfUsers; $i++){
            OrderReceiver::create([
                'name' => $faker->name(),
                'phone' => $faker->tollFreePhoneNumber(),
                'address' => $faker->address(),
                'p_id' => 1,
                'd_id' => 1,
                'w_id' => 1,
            ]);
        }

        for($i = 0; $i<$numberOfUsers; $i++){
            $phone = $faker->tollFreePhoneNumber();
            $email = $faker->unique()->safeEmail();
            $token = md5(config('app.name').'-'.$phone.'-'.$email.'-'.date('Y'));
            $token = substr_replace($token, '-', 6, 0);
            $token = substr_replace($token, '-', 13, 0);
            $token = substr_replace($token, '-', 21, 0);
            $token = substr_replace($token, '-', 29, 0);
            $shop = OrderShop::create([
                'name' => $faker->name(),
                'phone' => $phone,
                'email' => $email,
                'email_verified_at' => now(),
                'address' => $faker->address(),
                'password' => 'password', // password
                'remember_token' => Str::random(10),
                'menuroles' => 'shop',
                'api_token' => $token
            ]);
            $shop->assignRole($shopRole);
            OrderShopBank::create([
                'id' => $shop->id,
                'bank_name' => Str::random(10),
                'bank_branch' => Str::random(20),
                'stk_name' => Str::random(50),
                'stk' => Str::random(15),
                'cycle_cod' => 'tuesday_friday',
                'cycle_cod_day' => 3,
                'date_reconcile' => date('Y-m-d', strtotime('-31 day'))
            ]);
            OrderShopAddress::create([
                'shop_id' => $shop->id,
                'p_id' => 1,
                'd_id' => 1,
                'w_id' => 1,
                'type' => 'send',
                'name' => $faker->name(),
                'phone' => $faker->tollFreePhoneNumber(),
                'address' => $faker->address(),
            ]);
        }
    }
}