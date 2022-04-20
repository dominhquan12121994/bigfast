<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Services;

use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Spatie\Permission\Models\Role;

use App\Rules\PhoneRule;
use App\Modules\Orders\Constants\ShopConstant;

use App\Modules\Orders\Models\Repositories\Contracts\ShopStaffInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;
use App\Modules\Orders\Models\Entities\OrderShopToken;
use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Systems\Events\CreateLogEvents;

class ShopStaffServices
{
    protected $_shopsInterface;
    protected $_shopBankInterface;
    protected $_shopStaffInterface;
    protected $_shopAddressInterface;

    public function __construct(ShopsInterface $shopsInterface,
                                ShopBankInterface $shopBankInterface,
                                ShopStaffInterface $shopStaffInterface,
                                ShopAddressInterface $shopAddressInterface)
    {

        $this->_shopsInterface = $shopsInterface;
        $this->_shopBankInterface = $shopBankInterface;
        $this->_shopStaffInterface = $shopStaffInterface;
        $this->_shopAddressInterface = $shopAddressInterface;
    }

    public function crudStore($request)
    {
        if (!$request->has('shop_id')) {
            $dataRes = new \stdClass();
            $dataRes->result = false;
            return $dataRes;
        }

        try {
            DB::beginTransaction();

            $log_data = [];
            /** Tạo mới nhân viên thuộc shop */
            $shopStaff = $this->_shopStaffInterface->create(array(
                'shop_id' => $request->input('shop_id'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'password' => $request->input('password'),
                'menuroles' => 'shop_pushsale'
            ));

            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $shopStaff,
            ];

            /** Gán vai trò cho nhân viên */
            $role = Role::where('guard_name', '=', 'shop')->where('name', '=', 'shop_pushsale')->first();
            $shopStaff->assignRole($role);
            //
            DB::commit();

            //Lưu log
            event(new CreateLogEvents( $log_data, 'shop_staff', 'shop_staff_create' ));

            $dataRes = new \stdClass();
            $dataRes->result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();dd($message);
            $messageBag = new MessageBag;
            $messageBag->add('error', 'Thông tin không chính xác');

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function crudUpdate($request, $id)
    {
        try {
            DB::beginTransaction();

            $log_data = [];

            $fillter = array(
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'name' => $request->input('name')
            );

            if ( $request->has('password') ) {
                $fillter['password'] = $request->input('password');
            }

            $staff = $this->_shopStaffInterface->updateById($id, $fillter);

            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $staff,
            ];
            dd($log_data);
            //
            DB::commit();

            //Lưu log
            event(new CreateLogEvents( $log_data, 'shop_staff', 'shop_staff_update' ));

            $dataRes = new \stdClass();
            $dataRes->result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', 'Thông tin không chính xác');

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function getSearch($name, $limit = 1)
    {
        $q = '%'. $name . '%';
        if($limit == 1) {
            $query =  OrderShop::where('name', 'LIKE', $q)
            ->orwhere('phone', 'LIKE', $q)
            ->orwhere('email', 'LIKE', $q)
            ->first();
        } else {
            $query = OrderShop::where('name', 'LIKE', $q)
            ->orwhere('phone', 'LIKE', $q)
            ->orwhere('email', 'LIKE', $q)
            ->limit($limit)->get();
        }

        return $query;
    }

    public function getById($id, $limit = 1)
    {
        $q = $this->_shopsInterface->getById($id);

        return $q;
    }
}
