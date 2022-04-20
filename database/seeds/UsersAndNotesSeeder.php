<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Systems\Models\Entities\RoleHierarchy;

class UsersAndNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numberOfUsers = 0;
        $numberOfNotes = 0;
        $usersIds = array();
        $statusIds = array();
        $faker = Faker::create();
        $arrRole = array('accountancy', 'coordinator', 'pushsale', 'support', 'shipper', 'pickup', 'refund');

        /* Create roles admin */
        $adminRole = Role::create(['guard_name' => 'admin', 'name' => 'superadmin']);
        RoleHierarchy::create([
            'role_id' => $adminRole->id,
            'hierarchy' => 1,
        ]);

        /* Create roles admin */
        $adminRole = Role::create(['guard_name' => 'admin', 'name' => 'admin']);
        RoleHierarchy::create([
            'role_id' => $adminRole->id,
            'hierarchy' => 2,
        ]);
        /* Create roles kế toán */
        $userRole = Role::create(['guard_name' => 'admin', 'name' => 'accountancy']);
        RoleHierarchy::create([
            'role_id' => $userRole->id,
            'hierarchy' => 3,
        ]);
        /* Create roles điều phối */
        $userRole = Role::create(['guard_name' => 'admin', 'name' => 'coordinator']);
        RoleHierarchy::create([
            'role_id' => $userRole->id,
            'hierarchy' => 4,
        ]);
        /* Create roles giục đơn */
        $userRole = Role::create(['guard_name' => 'admin', 'name' => 'pushsale']);
        RoleHierarchy::create([
            'role_id' => $userRole->id,
            'hierarchy' => 5,
        ]);
        /* Create roles cskh */
        $userRole = Role::create(['guard_name' => 'admin', 'name' => 'support']);
        RoleHierarchy::create([
            'role_id' => $userRole->id,
            'hierarchy' => 6,
        ]);
        /* Create roles shipper */
        $userRole = Role::create(['guard_name' => 'admin', 'name' => 'shipper']);
        RoleHierarchy::create([
            'role_id' => $userRole->id,
            'hierarchy' => 7,
        ]);
        /* Create roles pickup */
        $userRole = Role::create(['guard_name' => 'admin', 'name' => 'pickup']);
        RoleHierarchy::create([
            'role_id' => $userRole->id,
            'hierarchy' => 8,
        ]);
        /* Create roles refund */
        $userRole = Role::create(['guard_name' => 'admin', 'name' => 'refund']);
        RoleHierarchy::create([
            'role_id' => $userRole->id,
            'hierarchy' => 9,
        ]);

        /*  insert users   */
        $user = User::create([ 
            'name' => 'electric',
            'phone' => '0966905659',
            'email' => 'electric@bigfast.vn',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'menuroles' => 'superadmin'
        ]);
        $user->assignRole('superadmin');

        /*  insert users   */
        $user = User::create([
            'name' => 'admin',
            'phone' => '0987654321',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'menuroles' => 'admin'
        ]);
        $user->assignRole('admin');
        for($i = 0; $i<$numberOfUsers; $i++){
            $role = $arrRole[rand(0, 6)];
            $user = User::create([
                'name' => $faker->name(),
                'phone' => $faker->tollFreePhoneNumber(),
                'email' => $faker->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
                'menuroles' => $role
            ]);
            $user->assignRole($role);
            array_push($usersIds, $user->id);
        }

        /*  insert status  */
//        DB::table('system_status')->insert([
//            'name' => 'ongoing',
//            'class' => 'badge badge-pill badge-primary',
//        ]);
//        array_push($statusIds, DB::getPdo()->lastInsertId());
//        DB::table('system_status')->insert([
//            'name' => 'stopped',
//            'class' => 'badge badge-pill badge-secondary',
//        ]);
//        array_push($statusIds, DB::getPdo()->lastInsertId());
//        DB::table('system_status')->insert([
//            'name' => 'completed',
//            'class' => 'badge badge-pill badge-success',
//        ]);
//        array_push($statusIds, DB::getPdo()->lastInsertId());
//        DB::table('system_status')->insert([
//            'name' => 'expired',
//            'class' => 'badge badge-pill badge-warning',
//        ]);
//        array_push($statusIds, DB::getPdo()->lastInsertId());
//
//        /*  insert notes  */
//        for($i = 0; $i<$numberOfNotes; $i++){
//            $noteType = $faker->word();
//            if(random_int(0,1)){
//                $noteType .= ' ' . $faker->word();
//            }
//            DB::table('system_notes')->insert([
//                'title'         => $faker->sentence(4,true),
//                'content'       => $faker->paragraph(3,true),
//                'status_id'     => $statusIds[random_int(0,count($statusIds) - 1)],
//                'note_type'     => $noteType,
//                'applies_to_date' => $faker->date(),
//                'users_id'      => $usersIds[random_int(0,$numberOfUsers-1)]
//            ]);
//        }
    }
}