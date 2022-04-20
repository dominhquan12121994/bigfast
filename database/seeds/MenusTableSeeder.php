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

        $this->insertLink('admin', 'Nhân viên', '/admin/users', 'cil-user');
        $this->insertLink('admin', 'Quản lý Shop', '/admin/shops', 'cil-baby');
        $this->insertLink('admin,accountancy,coordinator,pushsale,support,shipper,pickup,refund', 'Quản lý đơn hàng', '/admin/orders', 'cil-cart');
        $this->insertLink('admin', 'Yêu cầu hỗ trợ', '/admin/contacts', 'cil-contact');

        $this->beginDropdown('admin', 'Quản lý cước phí','cil-cash');
            $this->insertLink('admin', 'Gói cước','/admin/order-service');
            $this->insertLink('admin', 'Biểu phí','/admin/order-setting');
            $this->insertLink('admin', 'Phí thu hộ','/admin/order-setting/cod');
            $this->insertLink('admin', 'Phí lấy hàng','/admin/order-setting/fee-pick');
            $this->insertLink('admin', 'Phí bảo hiểm','/admin/order-setting/fee-insurance');
            $this->insertLink('admin', 'Tính phí vận chuyển','/admin/calculator-fee');
        $this->endDropdown();

        $this->beginDropdown('admin', 'Quản lý danh mục','cil-line-style');
            $this->insertLink('admin', 'Loại hỗ trợ', '/admin/contacts-type');
            $this->insertLink('admin', 'Bưu cục', '/admin/post-offices');
        $this->endDropdown();

        $this->beginDropdown('admin', 'Quản lý hành chính','cil-address-book');
            $this->insertLink('admin', 'Tỉnh thành','/admin/provinces');
            $this->insertLink('admin', 'Quận huyện', '/admin/districts');
            $this->insertLink('admin', 'Phường xã', '/admin/wards');
        $this->endDropdown();

        $this->insertLink('admin', 'Dòng tiền', '/admin/cash-flow', 'cil-cash');
        $this->insertLink('admin', 'Lịch sử đối soát', '/admin/reconcile-history', 'cil-history');
        $this->beginDropdown('admin', 'Quản lý mẫu','cil-barcode');
            $this->insertLink('admin', 'Mẫu in', '/admin/print-templates');
            $this->insertLink('admin', 'Mẫu mail', '/admin/mail');
        $this->endDropdown();
        $this->beginDropdown('admin', 'Báo cáo','cil-bar-chart');
            $this->insertLink('admin', 'Báo cáo vùng', '/admin/report/by-zone');
            $this->insertLink('admin', 'Báo cáo tiền thu hộ', '/admin/report/cod-report');
            $this->insertLink('admin', 'Báo cáo ship', '/admin/report/by-ship');
            $this->insertLink('admin', 'Báo cáo trạng thái', '/admin/report/by-status');
        $this->endDropdown();
        $this->insertLink('admin', 'Thông báo', '/admin/shop-notification', 'cil-alarm');
        $this->insertLink('admin', 'Nhật ký hệ thống', '/admin/system-log', 'cil-book');


        //Sidebar menu cho Shop
        $this->insertLink('shop', 'Thông tin Nhân viên', '/shop-staffs', 'cil-user');
        $this->insertLink('shop', 'Thông tin Shop', '/account', 'cil-user');
        $this->insertLink('shop', 'Quản lý đơn hàng', '/orders', 'cil-cart');
        $this->insertLink('shop', 'Báo cáo', '/reports','cil-bar-chart');
        $this->insertLink('shop', 'Yêu cầu hỗ trợ', '/contacts','cil-chat-bubble');
        $this->insertLink('shop', 'COD & Đối soát', '/cash-flow','cil-cash');

        //Sidebar menu cho nhân viên shop Shop
        $this->insertLink('shop_pushsale', 'Thông tin Nhân viên', '/staff', 'cil-user');
        $this->insertLink('shop_pushsale', 'Quản lý đơn hàng', '/order-staff', 'cil-cart');
        $this->insertLink('shop_pushsale', 'Yêu cầu hỗ trợ', '/contacts-staff', 'cil-chat-bubble');

        /* Create top menu */
        DB::table('system_menulist')->insert([
            'name' => 'top menu'
        ]);
        $this->menuId = DB::getPdo()->lastInsertId();  //set menuId

        $id = $this->beginDropdown('admin', 'Cài đặt');
        $id = $this->insertLink('admin', 'Cài đặt menu',      '/admin/menu/element');
        $id = $this->insertLink('admin', 'Phân quyền',              '/admin/roles');
        $this->endDropdown();

        $this->joinAllByTransaction(); ///   <===== Must by use on end of this seeder
    }
}
