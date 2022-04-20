<?php

use Illuminate\Database\Seeder;
//use database\seeds\UsersAndNotesSeeder;
//use database\seeds\ShopStaffSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call('UsersAndNotesSeeder');
        $this->call('FolderTableSeeder');
        $this->call('EmailSeeder');
        $this->call('PermissionsSeeder');
        $this->call('ZoneProvinceSeeder');
        $this->call('ZoneDistrictSeeder');
        $this->call('ZoneWardSeeder');
        $this->call('ShopsSeeder');
        $this->call('OrdersSeeder');
        $this->call('ShopStaffSeeder');
        $this->call('ContactsTypeTableSeeder');
        $this->call('OrderServiceSeeder');
        $this->call('PrintTemplateSettingSeeder');
        $this->call('MenusTableSeeder');
        $this->call('AdministrativeUnitKeywordSeeder');
        $this->call('ShopPurposeScaleSeeder');
    }
}
