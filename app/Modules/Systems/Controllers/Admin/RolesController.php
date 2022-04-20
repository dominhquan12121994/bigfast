<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

use App\Helpers\StringHelper;
use App\Modules\Systems\Models\Entities\Menurole;
use App\Modules\Systems\Models\Entities\RoleHierarchy;
use App\Modules\Systems\Constants\PermissionConstant;


class RolesController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = DB::table('system_roles')
        ->leftJoin('system_role_hierarchy', 'system_roles.id', '=', 'system_role_hierarchy.role_id')
        ->select('system_roles.*', 'system_role_hierarchy.hierarchy')
//        ->where('guard_name', 'admin')
        ->orderBy('hierarchy', 'asc')
        ->get();
        $rolesContant = PermissionConstant::roles;
        return view('Systems::roles.index', array(
            'roles' => $roles,
            'rolesContant' => $rolesContant,
        ));
    }

    public function moveUp(Request $request){
        $element = RoleHierarchy::where('role_id', '=', $request->input('id'))->first();
        $switchElement = RoleHierarchy::where('hierarchy', '<', $element->hierarchy)
            ->orderBy('hierarchy', 'desc')->first();
        if(!empty($switchElement)){
            $temp = $element->hierarchy;
            $element->hierarchy = $switchElement->hierarchy;
            $switchElement->hierarchy = $temp;
            $element->save();
            $switchElement->save();
        }
        return redirect()->route('admin.roles.index');
    }

    public function moveDown(Request $request){
        $element = RoleHierarchy::where('role_id', '=', $request->input('id'))->first();
        $switchElement = RoleHierarchy::where('hierarchy', '>', $element->hierarchy)
            ->orderBy('hierarchy', 'asc')->first();
        if(!empty($switchElement)){
            $temp = $element->hierarchy;
            $element->hierarchy = $switchElement->hierarchy;
            $switchElement->hierarchy = $temp;
            $element->save();
            $switchElement->save();
        }
        return redirect()->route('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Systems::roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = new Role();
        $role->name = $request->input('name');
        $role->guard_name = 'admin';
        $role->save();
        $hierarchy = RoleHierarchy::select('hierarchy')
        ->orderBy('hierarchy', 'desc')->first();
        if(empty($hierarchy)){
            $hierarchy = 0;
        }else{
            $hierarchy = $hierarchy['hierarchy'];
        }
        $hierarchy = ((integer)$hierarchy) + 1;
        $roleHierarchy = new RoleHierarchy();
        $roleHierarchy->role_id = $role->id;
        $roleHierarchy->hierarchy = $hierarchy;
        $roleHierarchy->save();
        \Func::setToast('Thành công', 'Thêm mới thành công quyền', 'notice');
        return redirect()->route('admin.roles.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $roles = PermissionConstant::roles;
        $permissions = PermissionConstant::permissions;
        $role = Role::where('id', '=', $id)->first();
        $guard_name = $role->guard_name;

        foreach($permissions as $module => $permission) {
            unset($permission['name']);
            foreach($permission as $action => $actionItem) {
                $perName = 'action_' . $module . '_' . $action;
                Permission::findOrCreate(StringHelper::vn_to_str($perName), $guard_name);
            }
        }

        return view('Systems::roles.show', array(
            'role' => $role,
            'roles' => $roles,
            'permissions' => $permissions,
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('Systems::roles.edit', array(
            'role' => Role::where('id', '=', $id)->first()
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::where('id', '=', $id)->first();
        $role->name = $request->input('name');
        $role->save();
        \Func::setToast('Thành công', 'Cập nhật thành công quyền', 'notice');
        return redirect()->route('admin.roles.edit', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $role = Role::where('id', '=', $id)->first();
        $roleHierarchy = RoleHierarchy::where('role_id', '=', $id)->first();
        $menuRole = Menurole::where('role_name', '=', $role->name)->first();
        $modelHasRole = DB::table('system_model_has_roles')->where('role_id', $id)->first();
        if(!empty($menuRole) || !empty($modelHasRole)){
            $request->session()->flash('message', "Can't delete. Role has assigned one or more menu elements.");
            $request->session()->flash('back', 'roles.index');
            return view('Systems::shared.universal-info');
        }else{
            $role->delete();
            $roleHierarchy->delete();
            $request->session()->flash('message', "Successfully deleted role");
            $request->session()->flash('back', 'roles.index');
            return view('Systems::shared.universal-info');
        }
    }
}
