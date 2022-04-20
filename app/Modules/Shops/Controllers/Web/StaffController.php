<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web;

use Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Rules\PhoneRule;
use App\Http\Controllers\Web\AbstractWebController;
use App\Modules\Orders\Constants\ShopConstant;

/**  */
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopStaffInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;

/**  */
use App\Modules\Operators\Models\Repositories\Contracts\WardsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;

use App\Modules\Orders\Models\Services\ShopStaffServices;
use App\Modules\Orders\Models\Services\ShopServices;
use App\Modules\Systems\Events\CreateLogEvents;
use App\Rules\PasswordRule;

class StaffController extends AbstractWebController
{

    protected $_shopStaffInterface;
    protected $_shopsInterface;
    protected $_shopBankInterface;
    protected $_shopAddressInterface;
    protected $_provincesInterface;
    protected $_districtsInterface;
    protected $_wardsInterface;
    protected $_shopServices;
    protected $_shopStaffServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ShopsInterface $shopsInterface,
                                ShopStaffInterface $shopStaffInterface,
                                ShopBankInterface $shopBankInterface,
                                ShopAddressInterface $shopAddressInterface,
                                ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                WardsInterface $wardsInterface,
                                ShopStaffServices $shopStaffServices,
                                ShopServices $shopServices)
    {
        parent::__construct();

        $this->_shopsInterface = $shopsInterface;
        $this->_shopStaffInterface = $shopStaffInterface;
        $this->_shopBankInterface = $shopBankInterface;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_wardsInterface = $wardsInterface;
        $this->_shopServices = $shopServices;
        $this->_shopStaffServices = $shopStaffServices;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $you = auth('shopStaff')->user();
        if (!$you) {
            abort(404);
        }
        $id = $you->id;

        $staff = $this->_shopStaffInterface->getById($id);

        return view('Shops::staff.index', [
            'staff' => $staff
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $you = auth('shopStaff')->user();
        if (!$you) {
            abort(404);
        }
        $staff = $this->_shopStaffInterface->getById($id);
        if (!$staff) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'phone' => array('required', 'unique:order_shops_staff,phone,'. $id, new PhoneRule()),
            'email' => 'required|email|unique:order_shops_staff,email,' . $id,
            'name' => 'required|max:255'
        ], [
            'name.unique' => 'Tên shop đã tồn tại',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'email.unique' => 'Địa chỉ email đã tồn tại',
            'email.email' => 'Địa chỉ email sai định dạng',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $dataRes = $this->_shopStaffServices->crudUpdate($request, $id);
        if (!$dataRes->result) {
            return redirect()->back()->withInput()->withErrors($dataRes->error);
        }

        \Func::setToast('Thành công', 'Cập nhật thành công thông tin nhân viên', 'notice');

        return redirect()->route('shop.staff.index');
    }
}
