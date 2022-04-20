<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Controllers\Api;

use Validator;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Helpers\StringHelper;
use App\Http\Controllers\Api\AbstractApiController;
use App\Modules\Systems\Models\Repositories\Contracts\RolesInterface;

class RolesController extends AbstractApiController
{

    protected $_rolesInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(RolesInterface $rolesInterface)
    {
        parent::__construct();

        $this->_rolesInterface = $rolesInterface;
    }

    public function permission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permission' => 'required|exists:system_permissions,name',
            'role' => 'required|exists:system_roles,name',
            'status' => 'required|in:0,1'
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        try {
            $status = (int)$request->input('status');
            $role = Role::where('name' , '=' , $request->input('role'))->first();
            $per = Permission::where('name', '=', StringHelper::vn_to_str($request->input('permission')))
                ->where('guard_name', $role->guard_name)
                ->first();

            if ($status) {
                $role->givePermissionTo($per);
            } else {
                $role->revokePermissionTo($per);
            }

        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;

            return $this->_responseError($dataRes);
        }

        return $this->_responseSuccess('Success');
    }

}
