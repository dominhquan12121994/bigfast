<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Controllers\Admin;

use Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Rules\PhoneRule;
use App\Rules\ExceptSpecialCharRule;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Systems\Constants\PermissionConstant;
use App\Modules\Systems\Models\Repositories\Contracts\RolesInterface;
use App\Rules\PasswordRule;
use App\Modules\Systems\Events\CreateLogEvents;

class UsersController extends Controller
{
    protected $_rolesInterface;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RolesInterface $rolesInterface)
    {
        $this->_rolesInterface = $rolesInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_users_view'))
        {
            abort(403);
        }

        $users = User::all();
        $rolesConfig = PermissionConstant::roles;

        return view('Systems::admin.usersList', compact('users', 'you',  'rolesConfig'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
        $user = User::find($id);
        return view('Systems::admin.userShow', compact( 'user' ));
    }

    /**
     * Show the form for create user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_users_create'))
        {
            abort(403);
        }

        $roles = $this->_rolesInterface->getMore(array('guard_name' => 'admin'));

        return view('Systems::admin.userCreateForm', array('roles' => $roles));
    }

    /**
     * Create user
     */
    public function store(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_users_create'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', new ExceptSpecialCharRule()],
            'phone' => ['required', 'unique:system_users', new PhoneRule()],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:system_users'],
            'password' => array('required', 'string', 'confirmed', new PasswordRule),
            'role' => ['required', 'min:1'],
            'role.*' => ['exists:system_roles,name'],
        ], [
            'password.confirmed' => 'M???t kh???u x??c nh???n kh??ng gi???ng !',
            'email.unique' => '?????a ch??? email ???? ???????c ????ng k??',
            'email.email' => '?????a ch??? email kh??ng h???p l???',
            'phone.unique' => 'S??? ??i???n tho???i ???? ????ng k??',
        ]);

        if ($validator->fails()) {
            \Func::setToast('Th???t b???i', 'Th??m m???i nh??n vi??n th???t b???i', 'error');
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $user =  User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'password' => Hash::make($request->input('password')),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'menuroles' => implode(',', $request->input('role'))
        ]);
        $user->assignRole($request->input('role'));
        \Func::setToast('Th??nh c??ng', 'Th??m m???i nh??n vi??n th??nh c??ng', 'notice');

        //Th??m d??? li???u log
        $log_data[] = [
            'model' => $user,
        ];

        //L??u log
        event(new CreateLogEvents($log_data, 'users', 'users_create' ));

        return redirect()->route('admin.users.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_users_update'))
        {
            abort(403);
        }

        $user = User::find($id);
        $roles = $this->_rolesInterface->getMore(array('guard_name' => 'admin'));

        return view('Systems::admin.userEditForm', compact(array(
            'user', 'roles'
        )));
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
        $you = auth('admin')->user();
        if (!$you->can('action_users_update'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', new ExceptSpecialCharRule()],
            'phone' => ['required', 'unique:system_users,phone,' . $id, new PhoneRule()],
            'email' => 'required|email|max:255|unique:system_users,email,' . $id,
            'password' => array('string', 'confirmed', new PasswordRule),
            'role' => ['required', 'min:1'],
            'role.*' => ['exists:system_roles,name'],
        ], [
            'password.confirmed' => 'M???t kh???u x??c nh???n kh??ng gi???ng !',
            'email.unique' => '?????a ch??? email ???? ???????c ????ng k??',
            'email.email' => '?????a ch??? email kh??ng h???p l???',
            'phone.unique' => 'S??? ??i???n tho???i ???? ????ng k??',
        ]);

        if ($validator->fails()) {
            \Func::setToast('Th???t b???i', 'C???p nh???t nh??n vi??n th???t b???i', 'error');
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $user = User::find($id);

        //Th??m d??? li???u log
        $log_data[] = [
            'old_data' => $user,
        ];

        //L??u log
        event(new CreateLogEvents($log_data, 'users', 'users_update' ));

        $user->name       = $request->input('name');
        $user->phone      = $request->input('phone');
        $user->email      = $request->input('email');
        $user->menuroles  = $request->input('role');
        if ($request->has('password')) {
            $user->password  = Hash::make($request->input('password'));
        }
        $user->save();

        $user->syncRoles($request->input('role'));
//        $user->assignRole($request->input('role'));

        \Func::setToast('Th??nh c??ng', 'C???p nh???t th??nh c??ng nh??n vi??n', 'notice');
        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_users_delete'))
        {
            abort(403);
        }

        $user = User::find($id);
        if($user){
            \Func::setToast('Th??nh c??ng', 'X??a nh??n vi??n th??nh c??ng', 'notice');
            $user->delete();
        }

        //L??u log
        event(new CreateLogEvents([], 'users', 'users_delete'));

        return redirect()->route('admin.users.index');
    }
}
