<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Repositories\Eloquent;

use App\Modules\Systems\Models\Repositories\Contracts\MenuInterface;
use App\Modules\Systems\Models\Entities\Menus;
use App\Modules\Systems\MenuBuilder\MenuBuilder;
use App\Modules\Systems\MenuBuilder\RenderFromDatabaseData;

class GetSidebarMenu implements MenuInterface{

    private $mb; //menu builder
    private $menu;

    public function __construct(){
        $this->mb = new MenuBuilder();
    }

    private function getMenuFromDB($menuId, $menuName){
        $this->menu = Menus::join('system_menu_role', 'system_menus.id', '=', 'system_menu_role.menus_id')
            ->select('system_menus.*')
            ->where('system_menus.menu_id', '=', $menuId)
            ->where('system_menu_role.role_name', '=', $menuName)
            ->orderBy('system_menus.sequence', 'asc')->get();
    }

    private function getGuestMenu( $menuId ){
        $this->getMenuFromDB($menuId, 'guest');
    }

    private function getUserMenu( $menuId ){
        $this->getMenuFromDB($menuId, 'user');
    }

    private function getAdminMenu( $menuId ){
        $this->getMenuFromDB($menuId, 'admin');
    }

    public function get($role, $menuId=2){
        $this->getMenuFromDB($menuId, $role);
        $rfd = new RenderFromDatabaseData;
        return $rfd->render($this->menu);
        /*
        $roles = explode(',', $roles);
        if(empty($roles)){
            $this->getGuestMenu( $menuId );
        }elseif(in_array('admin', $roles)){
            $this->getAdminMenu( $menuId );
        }elseif(in_array('user', $roles)){
            $this->getUserMenu( $menuId );
        }else{
            $this->getGuestMenu( $menuId );
        }
        $rfd = new RenderFromDatabaseData;
        return $rfd->render($this->menu);
        */
    }

    public function getAll( $menuId=2 ){
        $this->menu = Menus::select('system_menus.*')
            ->where('system_menus.menu_id', '=', $menuId)
            ->orderBy('system_menus.sequence', 'asc')->get();
        $rfd = new RenderFromDatabaseData;
        return $rfd->render($this->menu);
    }
}
