<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Modules\Systems\Models\Entities\Menulist;
use App\Modules\Systems\Models\Entities\RoleHierarchy;
use App\Modules\Systems\Models\Repositories\Eloquent\GetSidebarMenu;

class GetShopMenu
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
        if (Auth::guard('shop')->check() || Auth::guard('shopStaff')->check()){
            $role = 'guest';
            $user = Auth::guard('shop')->check() ? Auth::guard('shop')->user() : Auth::guard('shopStaff')->user();
            $userRoles = $user->getRoleNames();
            $roleHierarchy = RoleHierarchy::select('system_role_hierarchy.role_id', 'system_roles.name')
            ->join('system_roles', 'system_roles.id', '=', 'system_role_hierarchy.role_id')
            ->orderBy('system_role_hierarchy.hierarchy', 'asc')->get();
            $flag = false;
            foreach($roleHierarchy as $roleHier){
                foreach($userRoles as $userRole){
                    if($userRole == $roleHier['name']){
                        $role = $userRole;
                        $flag = true;
                        break;
                    }
                }
                if($flag === true){
                    break;
                }
            }
        } else {
            $role = 'guest';
        }
        //session(['prime_user_role' => $role]);
        $menus = new GetSidebarMenu();
        $menulists = Menulist::all();
        $result = array();
        foreach($menulists as $menulist){
            $result[ $menulist->name ] = $menus->get( $role, $menulist->id );
        }
        view()->share('appMenus', $result );
        return $next($request);
    }
}
