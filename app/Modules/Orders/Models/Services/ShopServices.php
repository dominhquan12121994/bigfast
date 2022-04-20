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

use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;
use App\Modules\Orders\Models\Entities\OrderShopToken;
use App\Modules\Orders\Models\Entities\OrderShop;
use App\Modules\Systems\Events\CreateLogEvents;

class ShopServices
{
    protected $_shopsInterface;
    protected $_shopBankInterface;
    protected $_shopAddressInterface;

    public function __construct(ShopsInterface $shopsInterface,
                                ShopBankInterface $shopBankInterface,
                                ShopAddressInterface $shopAddressInterface)
    {

        $this->_shopsInterface = $shopsInterface;
        $this->_shopBankInterface = $shopBankInterface;
        $this->_shopAddressInterface = $shopAddressInterface;
    }

    public function crudStore($request)
    {
        try {
            DB::beginTransaction();

            $log_data = [];
            /** Tạo mới shop */
            $token = md5(config('app.name').'-'.$request->input('phone').'-'.$request->input('email').'-'.date('Y'));
            $token = substr_replace($token, '-', 6, 0);
            $token = substr_replace($token, '-', 13, 0);
            $token = substr_replace($token, '-', 21, 0);
            $token = substr_replace($token, '-', 29, 0);
            $shop = $this->_shopsInterface->create(array(
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'name' => trim($request->input('name')),
                'address' => $request->input('address', '') ?: '',
                'password' => $request->input('password'),
                'api_token' => $token
            ));

            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $shop,
            ];

            /** Gán vai trò cho Shop */
            $role = Role::where('name', '=', 'shop')->first();
            $shop->assignRole($role);

            /** Add thông tin ngân hàng cho shop */
            $branch = $request->input('branch', '');
            $scale = $request->input('scale', '');
            $purpose = $request->input('purpose', '');
            if ( is_array($branch) ) {
                $branch = implode(',', $branch);
            }
            $request->merge(array('id' => $shop->id));
            $cycle_cod = $request->input('cycle_cod', 'once_per_month');
            $cycle_cod_day = (isset(ShopConstant::bank['cycle_cod'][$cycle_cod])) ? ShopConstant::bank['cycle_cod'][$cycle_cod]['days'] : 30;
            $shopBank = $this->_shopBankInterface->create(array(
                'id' => $shop->id,
                'bank_name' => $request->input('bank_name', '') ?: '',
                'bank_branch' => $request->input('bank_branch', '') ?: '',
                'cycle_cod' => $cycle_cod,
                'cycle_cod_day' => $cycle_cod_day,
                'stk_name' => $request->input('stk_name', '') ?: '',
                'stk' => $request->input('stk', '') ?: '',
                'date_reconcile' => date('Y-m-d', strtotime('-1 day')),
                'scale' => $scale,
                'purpose' => $purpose,
                'branch' => $branch,
            ));
            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $shopBank,
            ];

            if ($request->has('addName')) {
                $addName = $request->input('addName');
                $addPhone = $request->input('addPhone');
                $addAddress = $request->input('addAddress');
                $addProvinces = $request->input('addProvinces');
                $addDistricts = $request->input('addDistricts');
                $addWards = $request->input('addWards');

                foreach ($addName as $key => $name) {
                    $phone = $addPhone[$key];
                    $address = $addAddress[$key];
                    $province = $addProvinces[$key];
                    $district = $addDistricts[$key];
                    $ward = $addWards[$key];
                    $default = (count($addName) === 1) ? 1 : 0;

                    if ($name && $phone && $address) {
                        if (substr($phone, 0, 1) !== '0') $phone = '0' . $phone;
                        $shopAddress = $this->_shopAddressInterface->create(array(
                            'shop_id' => $shop->id,
                            'name' => $name,
                            'phone' => $phone,
                            'address' => $address,
                            'p_id' => $province,
                            'd_id' => $district,
                            'w_id' => $ward,
                            'default' => $default,
                        ));

                        //Thêm dữ liệu log
                        $log_data[] = [
                            'model' => $shopAddress,
                        ];

                    }
                }
            }
            //
            DB::commit();

            //Lưu log
            event(new CreateLogEvents( $log_data, 'shops', 'shops_create' ));

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

    public function crudUpdate($request, $id)
    {
        try {
            DB::beginTransaction();

            $log_data = [];

            $shop = $this->_shopsInterface->updateById($id, array(
                // 'phone' => $request->input('phone'),
                // 'email' => $request->input('email'),
                'name' => $request->input('name'),
                'address' => $request->input('address', '')
            ));


            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $shop,
            ];

            $branch = $request->input('branch', '');
            $scale = $request->input('scale', '');
            $purpose = $request->input('purpose', '');
            if ( is_array($branch) ) {
                $branch = implode(',', $branch);
            }
            $cycle_cod = $request->input('cycle_cod', 'once_per_month');
            $cycle_cod_day = (isset(ShopConstant::bank['cycle_cod'][$cycle_cod])) ? ShopConstant::bank['cycle_cod'][$cycle_cod]['days'] : 30;
            $shopBank = $this->_shopBankInterface->updateById($id, array(
                'bank_name' => $request->input('bank_name', ''),
                'bank_branch' => $request->input('bank_branch', ''),
                'services' => implode(',', $request->input('services', [])),
                'cycle_cod' => $cycle_cod,
                'cycle_cod_day' => $cycle_cod_day,
                'stk_name' => $request->input('stk_name', ''),
                'stk' => $request->input('stk', ''),
                'scale' => $scale,
                'purpose' => $purpose,
                'branch' => $branch,
            ));

            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $shopBank,
            ];

            $addIds = $request->input('addIds');
            $checkExists = $this->_shopAddressInterface->checkExist(array('shop_id' => $id));
            if ($addIds) {
                if ($checkExists !== count($addIds)) {
                    $shopAddress = $this->_shopAddressInterface->delByCond(array('shop_id' => $id, 'id_not_in' => $addIds));
                    //Thêm dữ liệu log
                    $log_data[] = [
                        'model' => $this->_shopAddressInterface,
                    ];
                }
            } else {
                if ($checkExists) {
                    // delete all address by shop
                    $shopAddress = $this->_shopAddressInterface->delByCond(array('shop_id' => $id));
                    //Thêm dữ liệu log
                    $log_data[] = [
                        'model' => $this->_shopAddressInterface,
                    ];
                }
            }

            if ($request->has('addName')) {
                $addIds = $request->input('addIds');
                $addName = $request->input('addName');
                $addPhone = $request->input('addPhone');
                $addAddress = $request->input('addAddress');
                $addProvinces = $request->input('addProvinces');
                $addDistricts = $request->input('addDistricts');
                $addWards = $request->input('addWards');
                $addDefault = (int) $request->input('addDefault', 0);

                foreach ($addName as $key => $name) {
                    $phone = $addPhone[$key];
                    $address = $addAddress[$key];
                    $province = $addProvinces[$key];
                    $district = $addDistricts[$key];
                    $ward = $addWards[$key];
                    $default = ($key === $addDefault) ? 1 : 0;
                    if (count($addName) === 1) $default = 1;

                    if (isset($addIds[$key])) {
                        $shopAddress = $this->_shopAddressInterface->getById($addIds[$key]);
                        if (substr($phone, 0, 1) !== '0') $phone = '0' . $phone;
                        $this->_shopAddressInterface->updateById($addIds[$key], array(
                            'name' => $name,
                            'phone' => $phone,
                            'address' => $address,
                            'p_id' => $province,
                            'd_id' => $district,
                            'w_id' => $ward,
                            'default' => $default,
                        ));

                        //Thêm dữ liệu log
                        $log_data[] = [
                            'old_data' => $shopAddress
                        ];
                    } else {
                        if ($name && $phone && $address) {
                            if (substr($phone, 0, 1) !== '0') $phone = '0' . $phone;
                            $shopAddress = $this->_shopAddressInterface->create(array(
                                'shop_id' => $shop->id,
                                'name' => $name,
                                'phone' => $phone,
                                'address' => $address,
                                'p_id' => $province,
                                'd_id' => $district,
                                'w_id' => $ward,
                                'default' => $default,
                            ));

                            //Thêm dữ liệu log
                            $log_data[] = [
                                'model' => $shopAddress,
                            ];
                        }
                    }
                }
            }
            //
            DB::commit();

            //Lưu log
            event(new CreateLogEvents( $log_data, 'shops', 'shops_update' ));

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

    public function crudUpdateByPath($request, $id)
    {
        try {
            DB::beginTransaction();

            $log_data = [];
            $fillShop = array();

            $fillShop = $request->only(['name', 'address']);
            if ( !empty($fillShop) ) {
                $shop = $this->_shopsInterface->updateById($id, $fillShop);
                //Thêm dữ liệu log
                $log_data[] = [
                    'model' => $shop,
                ];
            }

            $fillShopBank = $request->only(['scale', 'purpose', 'bank_name', 'bank_branch', 'stk_name', 'stk']);
            if ($request->has('branch')) {
                $fillShopBank['branch'] = $request->input('branch', '');
                if ( is_array( $fillShopBank['branch']) ) {
                    $fillShopBank['branch'] = implode(',', $fillShopBank['branch']);
                }
            }
            if ($request->has('cycle_cod')) {
                $fillShopBank['cycle_cod'] = $request->input('cycle_cod', 'once_per_month');
                $fillShopBank['cycle_cod_day'] = (isset(ShopConstant::bank['cycle_cod'][$fillShopBank['cycle_cod']])) ? ShopConstant::bank['cycle_cod'][$fillShopBank['cycle_cod']]['days'] : 30;
            }
            if ( !empty($fillShopBank) ) {
                $shopBank = $this->_shopBankInterface->updateById($id, $fillShopBank);
                //Thêm dữ liệu log
                $log_data[] = [
                    'model' => $shopBank,
                ];
            }

            if ($request->has('addIds')) {
                $addIds = $request->input('addIds');
                $checkExists = $this->_shopAddressInterface->checkExist(array('shop_id' => $id));
                if ($addIds) {
                    if ($checkExists !== count($addIds)) {
                        $shopAddress = $this->_shopAddressInterface->delByCond(array('shop_id' => $id, 'id_not_in' => $addIds));
                        //Thêm dữ liệu log
                        $log_data[] = [
                            'model' => $this->_shopAddressInterface,
                        ];
                    }
                } else {
                    if ($checkExists) {
                        // delete all address by shop
                        $shopAddress = $this->_shopAddressInterface->delByCond(array('shop_id' => $id));
                        //Thêm dữ liệu log
                        $log_data[] = [
                            'model' => $this->_shopAddressInterface,
                        ];
                    }
                }

                if ($request->has('addName')) {
                    $addIds = $request->input('addIds');
                    $addName = $request->input('addName');
                    $addPhone = $request->input('addPhone');
                    $addAddress = $request->input('addAddress');
                    $addProvinces = $request->input('addProvinces');
                    $addDistricts = $request->input('addDistricts');
                    $addWards = $request->input('addWards');
                    $addDefault = (int) $request->input('addDefault', 0);

                    foreach ($addName as $key => $name) {
                        $phone = $addPhone[$key];
                        $address = $addAddress[$key];
                        $province = $addProvinces[$key];
                        $district = $addDistricts[$key];
                        $ward = $addWards[$key];
                        $default = ($key === $addDefault) ? 1 : 0;
                        if (count($addName) === 1) $default = 1;

                        if (isset($addIds[$key])) {
                            $shopAddress = $this->_shopAddressInterface->getById($addIds[$key]);
                            if (substr($phone, 0, 1) !== '0') $phone = '0' . $phone;
                            $this->_shopAddressInterface->updateById($addIds[$key], array(
                                'name' => $name,
                                'phone' => $phone,
                                'address' => $address,
                                'p_id' => $province,
                                'd_id' => $district,
                                'w_id' => $ward,
                                'default' => $default,
                            ));

                            //Thêm dữ liệu log
                            $log_data[] = [
                                'old_data' => $shopAddress
                            ];
                        } else {
                            if ($name && $phone && $address) {
                                if (substr($phone, 0, 1) !== '0') $phone = '0' . $phone;
                                $shopAddress = $this->_shopAddressInterface->create(array(
                                    'shop_id' => $id,
                                    'name' => $name,
                                    'phone' => $phone,
                                    'address' => $address,
                                    'p_id' => $province,
                                    'd_id' => $district,
                                    'w_id' => $ward,
                                    'default' => $default,
                                ));

                                //Thêm dữ liệu log
                                $log_data[] = [
                                    'model' => $shopAddress,
                                ];
                            }
                        }
                    }
                }
            }

            //
            DB::commit();

            //Lưu log
            event(new CreateLogEvents( $log_data, 'shops', 'shops_update' ));

            $dataRes = new \stdClass();
            $dataRes->result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = 'Thông tin không chính xác';
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
