<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web;

use Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Web\AbstractWebController;
use App\Modules\Orders\Constants\ShopConstant;

/**  */
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;

/**  */
use App\Modules\Operators\Models\Repositories\Contracts\WardsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;

use App\Modules\Orders\Models\Services\ShopServices;

use App\Rules\PhoneRule;
use App\Rules\ExceptSpecialCharRule;
use App\Modules\Systems\Events\CreateLogEvents;

class ShopsController extends AbstractWebController
{

    protected $_shopsInterface;
    protected $_shopBankInterface;
    protected $_shopAddressInterface;
    protected $_provincesInterface;
    protected $_districtsInterface;
    protected $_wardsInterface;
    protected $_shopServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ShopsInterface $shopsInterface,
                                ShopBankInterface $shopBankInterface,
                                ShopAddressInterface $shopAddressInterface,
                                ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                WardsInterface $wardsInterface,
                                ShopServices $shopServices)
    {
        parent::__construct();

        $this->_shopsInterface = $shopsInterface;
        $this->_shopBankInterface = $shopBankInterface;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_wardsInterface = $wardsInterface;
        $this->_shopServices = $shopServices;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $you = auth('shop')->user();
        $id = $you->id;

        $addDistricts = $addWards = array();
        $shop = $this->_shopsInterface->getById($id);
        if (!$shop) {
            abort(404);
        }
        $shopBank = $this->_shopBankInterface->getById($id);
        $shopAddress = $this->_shopAddressInterface->getMore(array('shop_id' => $id));
        if (count($shopAddress) > 0) {
            foreach ($shopAddress as $key => $address) {
                $addDistricts[$key] = $this->_districtsInterface->getMore(array('p_id' => $address->p_id));
                $addWards[$key] = $this->_wardsInterface->getMore(array('d_id' => $address->d_id));
            }
        }

        $provinces = $this->_provincesInterface->getMore();
        $districts = $this->_districtsInterface->getMore(array('p_id' => $provinces[0]->id));
        $wards = $this->_wardsInterface->getMore(array('d_id' => $districts[0]->id));

        return view('Shops::shops.edit', [
            'shop' => $shop,
            'shopBank' => $shopBank,
            'shopAddress' => $shopAddress,
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards,
            'addWards' => $addWards,
            'addDistricts' => $addDistricts,
            'cycleCodList' => ShopConstant::bank['cycle_cod'],
            'createOrder' => $request->has('create-order')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $you = auth('shop')->user();
        $id = $you->id;

        $validator = Validator::make($request->all(), [
            'phone' => array('required', 'unique:order_shops,phone,' . $id, new PhoneRule()),
            'email' => 'required|email|unique:order_shops,email,' . $id,
            'name' => 'required|max:255',
            'address' => array('required', new ExceptSpecialCharRule()),
            'bank_name' => array('required', new ExceptSpecialCharRule()),
            'bank_branch' => array('required', new ExceptSpecialCharRule()),
            'stk_name' => array('required', new ExceptSpecialCharRule()),
            'stk' => array('required', new ExceptSpecialCharRule()),
            'addName' => 'required|min:1',
            'addPhone.*' => array('required', new PhoneRule()),
            'addName.*' => array('required', 'max:255', new ExceptSpecialCharRule()),
            'addAddress.*' => array('required', 'max:255', new ExceptSpecialCharRule())
        ], [
            'addName.required' => 'Phải có ít nhất 1 địa chỉ lấy hàng',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'email.unique' => 'Địa chỉ email đã tồn tại',
            'email.email' => 'Địa chỉ email sai định dạng',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $this->_shopServices->crudUpdate($request, $id);
        \Func::setToast('Thành công', 'Cập nhật thành công thông tin Shop', 'notice');

        if ($request->has('create-order')) {
            return redirect()->route('admin.orders.create', array('shop_id' => $id));
        }
        return redirect()->route('shop.profile.edit');
    }
}
