<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AbstractApiController;
use App\Modules\Systems\Models\Repositories\Contracts\UsersInterface;
use Spatie\Permission\Models\Role;

use App\Modules\Systems\Resources\UsersResource;
use App\Modules\Systems\Resources\UsersFindResource;

use App\Modules\Systems\Models\Entities\User;

class UsersController extends AbstractApiController
{

    protected $_usersInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UsersInterface $usersInterface)
    {
        parent::__construct();

        $this->_usersInterface = $usersInterface;
    }

    /**
     * Display a listing by provinces.
     *
     * @return \Illuminate\Http\Response
     */
    public function findByRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roles' => 'required|exists:system_roles,name'
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Yêu cầu từ khoá tối thiểu 3 ký tự');
        }

        $users = User::role($request->input('roles'))->get();

        return $this->_responseSuccess('Success', new UsersResource($users));
    }

    public function findByText(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Yêu cầu từ khoá tối thiểu 3 ký tự');
        }

        $users = $this->_usersInterface->getMore(
            array(
                'name'  => $request->input('search'),
                'email' => $request->input('search'),
            )
        );

        return $this->_responseSuccess('Success', new UsersFindResource($users));
    }

    public function findByPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Yêu cầu từ khoá tối thiểu 3 ký tự');
        }

        $search = $request->input('search');

        $users = User::permission($search)->get();

        return $this->_responseSuccess('Success', new UsersFindResource($users));
    }
}
