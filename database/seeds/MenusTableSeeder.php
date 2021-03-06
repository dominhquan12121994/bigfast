<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\StringHelper;

class MenusTableSeeder extends Seeder
{
    private $menuId = null;
    private $dropdownId = array();
    private $dropdown = false;
    private $sequence = 1;
    private $joinData = array();
    private $adminRole = null;
    private $userRole = null;
    private $subFolder = '';

    public function join($roles, $menusId){
        $roles = explode(',', $roles);
        foreach($roles as $role){
            array_push($this->joinData, array('role_name' => $role, 'menus_id' => $menusId));
        }
    }

    /*
        Function assigns menu elements to roles
        Must by use on end of this seeder
    */
    public function joinAllByTransaction(){
        DB::beginTransaction();
        foreach($this->joinData as $data){
            DB::table('system_menu_role')->insert([
                'role_name' => $data['role_name'],
                'menus_id' => $data['menus_id'],
            ]);
        }
        DB::commit();
    }

    public function insertLink($roles, $name, $href, $icon = null){
        $href = $this->subFolder . $href;
        if($this->dropdown === false){
            DB::table('system_menus')->insert([
                'slug' => 'link',
                'name' => $name,
                'icon' => $icon,
                'href' => $href,
                'menu_id' => $this->menuId,
                'sequence' => $this->sequence
            ]);
        }else{
            DB::table('system_menus')->insert([
                'slug' => 'link',
                'name' => $name,
                'icon' => $icon,
                'href' => $href,
                'menu_id' => $this->menuId,
                'parent_id' => $this->dropdownId[count($this->dropdownId) - 1],
                'sequence' => $this->sequence
            ]);
        }
        $this->sequence++;
        $lastId = DB::getPdo()->lastInsertId();
        $this->join($roles, $lastId);
        $permission = Permission::where('name', '=', 'visit_' . StringHelper::vn_to_str($name))->get();
        if(count($permission) === 0){
            $permission = Permission::create(['name' => 'visit_' . StringHelper::vn_to_str($name)]);
        }
        $roles = explode(',', $roles);
//        if(in_array('user', $roles)){
//            $this->userRole->givePermissionTo($permission);
//        }
        if(in_array('admin', $roles)){
            $this->adminRole->givePermissionTo($permission);
        }
        return $lastId;
    }

    public function insertTitle($roles, $name){
        DB::table('system_menus')->insert([
            'slug' => 'title',
            'name' => $name,
            'menu_id' => $this->menuId,
            'sequence' => $this->sequence
        ]);
        $this->sequence++;
        $lastId = DB::getPdo()->lastInsertId();
        $this->join($roles, $lastId);
        return $lastId;
    }

    public function beginDropdown($roles, $name, $icon = ''){
        if(count($this->dropdownId)){
            $parentId = $this->dropdownId[count($this->dropdownId) - 1];
        }else{
            $parentId = null;
        }
        DB::table('system_menus')->insert([
            'slug' => 'dropdown',
            'name' => $name,
            'icon' => $icon,
            'menu_id' => $this->menuId,
            'sequence' => $this->sequence,
            'parent_id' => $parentId
        ]);
        $lastId = DB::getPdo()->lastInsertId();
        array_push($this->dropdownId, $lastId);
        $this->dropdown = true;
        $this->sequence++;
        $this->join($roles, $lastId);
        return $lastId;
    }

    public function endDropdown(){
        $this->dropdown = false;
        array_pop( $this->dropdownId );
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Get roles */
        $this->adminRole = Role::where('name' , '=' , 'admin' )->first();
//        $this->userRole = Role::where('name', '=', 'user' )->first();
        /* Create Sidebar menu */
        DB::table('system_menulist')->insert([
            'name' => 'sidebar menu'
        ]);
        $this->menuId = DB::getPdo()->lastInsertId();  //set menuId

        $this->insertLink('admin', 'Nh??n vi??n', '/admin/users', 'cil-user');
        $this->insertLink('admin', 'Qu???n l?? Shop', '/admin/shops', 'cil-baby');
        $this->insertLink('admin,accountancy,coordinator,pushsale,support,shipper,pickup,refund', 'Qu???n l?? ????n h??ng', '/admin/orders', 'cil-cart');
        $this->insertLink('admin', 'Y??u c???u h??? tr???', '/admin/contacts', 'cil-contact');

        $this->beginDropdown('admin', 'Qu???n l?? c?????c ph??','cil-cash');
            $this->insertLink('admin', 'G??i c?????c','/admin/order-service');
            $this->insertLink('admin', 'Bi???u ph??','/admin/order-setting');
            $this->insertLink('admin', 'Ph?? thu h???','/admin/order-setting/cod');
            $this->insertLink('admin', 'Ph?? l???y h??ng','/admin/order-setting/fee-pick');
            $this->insertLink('admin', 'Ph?? b???o hi???m','/admin/order-setting/fee-insurance');
            $this->insertLink('admin', 'T??nh ph?? v???n chuy???n','/admin/calculator-fee');
        $this->endDropdown();

        $this->beginDropdown('admin', 'Qu???n l?? danh m???c','cil-line-style');
            $this->insertLink('admin', 'Lo???i h??? tr???', '/admin/contacts-type');
            $this->insertLink('admin', 'B??u c???c', '/admin/post-offices');
        $this->endDropdown();

        $this->beginDropdown('admin', 'Qu???n l?? h??nh ch??nh','cil-address-book');
            $this->insertLink('admin', 'T???nh th??nh','/admin/provinces');
            $this->insertLink('admin', 'Qu???n huy???n', '/admin/districts');
            $this->insertLink('admin', 'Ph?????ng x??', '/admin/wards');
        $this->endDropdown();

        $this->insertLink('admin', 'D??ng ti???n', '/admin/cash-flow', 'cil-cash');
        $this->insertLink('admin', 'L???ch s??? ?????i so??t', '/admin/reconcile-history', 'cil-history');
        $this->beginDropdown('admin', 'Qu???n l?? m???u','cil-barcode');
            $this->insertLink('admin', 'M???u in', '/admin/print-templates');
            $this->insertLink('admin', 'M???u mail', '/admin/mail');
        $this->endDropdown();
        $this->beginDropdown('admin', 'B??o c??o','cil-bar-chart');
            $this->insertLink('admin', 'B??o c??o v??ng', '/admin/report/by-zone');
            $this->insertLink('admin', 'B??o c??o ti???n thu h???', '/admin/report/cod-report');
            $this->insertLink('admin', 'B??o c??o ship', '/admin/report/by-ship');
            $this->insertLink('admin', 'B??o c??o tr???ng th??i', '/admin/report/by-status');
        $this->endDropdown();
        $this->insertLink('admin', 'Th??ng b??o', '/admin/shop-notification', 'cil-alarm');
        $this->insertLink('admin', 'Nh???t k?? h??? th???ng', '/admin/system-log', 'cil-book');


        //Sidebar menu cho Shop
        $this->insertLink('shop', 'Th??ng tin Nh??n vi??n', '/shop-staffs', 'cil-user');
        $this->insertLink('shop', 'Th??ng tin Shop', '/account', 'cil-user');
        $this->insertLink('shop', 'Qu???n l?? ????n h??ng', '/orders', 'cil-cart');
        $this->insertLink('shop', 'B??o c??o', '/reports','cil-bar-chart');
        $this->insertLink('shop', 'Y??u c???u h??? tr???', '/contacts','cil-chat-bubble');
        $this->insertLink('shop', 'COD & ?????i so??t', '/cash-flow','cil-cash');

        //Sidebar menu cho nh??n vi??n shop Shop
        $this->insertLink('shop_pushsale', 'Th??ng tin Nh??n vi??n', '/staff', 'cil-user');
        $this->insertLink('shop_pushsale', 'Qu???n l?? ????n h??ng', '/order-staff', 'cil-cart');
        $this->insertLink('shop_pushsale', 'Y??u c???u h??? tr???', '/contacts-staff', 'cil-chat-bubble');

        /* Create top menu */
        DB::table('system_menulist')->insert([
            'name' => 'top menu'
        ]);
        $this->menuId = DB::getPdo()->lastInsertId();  //set menuId

        $id = $this->beginDropdown('admin', 'C??i ?????t');
        $id = $this->insertLink('admin', 'C??i ?????t menu',      '/admin/menu/element');
        $id = $this->insertLink('admin', 'Ph??n quy???n',              '/admin/roles');
        $this->endDropdown();

        $this->joinAllByTransaction(); ///   <===== Must by use on end of this seeder
    }
}
