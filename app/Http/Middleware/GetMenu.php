<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Modules\Systems\Models\Entities\Menulist;
use App\Modules\Systems\Models\Entities\RoleHierarchy;
use App\Modules\Systems\Models\Repositories\Eloquent\GetSidebarMenu;

class GetMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $result = array();
        if (Auth::guard('admin')->check()){
            $userRoles = Auth::guard('admin')->user()->getRoleNames();
//            $roleHierarchy = RoleHierarchy::select('system_role_hierarchy.role_id', 'system_roles.name')
//            ->join('system_roles', 'system_roles.id', '=', 'system_role_hierarchy.role_id')
//            ->orderBy('system_role_hierarchy.hierarchy', 'asc')->get();
//            $flag = false;
//            foreach($roleHierarchy as $roleHier){
//                foreach($userRoles as $userRole){
//                    if($userRole == $roleHier['name']){
//                        $role = $userRole;
//                        $flag = true;
//                        break;
//                    }
//                }
//                if($flag === true){
//                    break;
//                }
//            }
            $role = $userRoles[0];
            if ($role === 'superadmin') $role = 'admin';
            $menus = new GetSidebarMenu();
            $menuLists = Menulist::all();
            foreach($menuLists as $menuList){
                $result[ $menuList->name ] = $menus->get( $role, $menuList->id );
            }
        }
        view()->share('appMenus', $result);
        return $next($request);
    }
}