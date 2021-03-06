<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Api;

use Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Rules\PhoneRule;
use App\Http\Controllers\Api\AbstractApiController;
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
use App\Modules\Shops\Resources\ShopStaffResource;

class ShopStaffController extends AbstractApiController
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = '';
        $you = $request->user();
        if (!$you) {
            abort(404);
        }
        $shop_id = $you->id;
        $filter = array('shop_id' => $shop_id);

        $staffs = $this->_shopStaffInterface->getMore($filter, array());

        return $this->_responseSuccess('Success', new ShopStaffResource($staffs) ); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $you = $request->user();
        if (!$you) {
            abort(404);
        }
        $shop_id = $you->id;
        request()->merge(['shop_id' => $shop_id]);

        $validator = Validator::make($request->all(), [
            'phone' => array('required', 'unique:order_shops_staff', new PhoneRule()),
            'email' => 'required|email|unique:order_shops_staff|max:255',
            'name' => 'required|max:255',
            'password' => array('required', new PasswordRule(), 'confirmed' )
        ],
        [
            'name.unique' => 'T??n shop ???? t???n t???i',
            'phone.unique' => 'S??? ??i???n tho???i ???? t???n t???i',
            'email.unique' => '?????a ch??? email ???? t???n t???i',
            'email.email' => '?????a ch??? email sai ?????nh d???ng',
            'password.confirmed' => 'X??c nh???n m???t kh???u kh??ng ????ng',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        $dataRes = $this->_shopStaffServices->crudStore($request);
        if (!$dataRes->result) {
            return $this->_responseError($dataRes->error);
        }

        return $this->_responseSuccess('Success', 'Th??m m???i th??nh c??ng nh??n vi??n' ); 
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
        $you = $request->user();
        if (!$you) {
            abort(404);
        }
        $id = $request->input('id', 0);
        $staff = $this->_shopStaffInterface->getById($id);
        if (!$staff) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'phone' => array('required', 'unique:order_shops_staff,phone,'. $id, new PhoneRule()),
            'email' => 'required|email|unique:order_shops_staff,email,' . $id,
            'password' => array('string', new PasswordRule, 'confirmed'),
            'name' => 'required|max:255'
        ], [
            'name.unique' => 'T??n shop ???? t???n t???i',
            'phone.unique' => 'S??? ??i???n tho???i ???? t???n t???i',
            'email.unique' => '?????a ch??? email ???? t???n t???i',
            'email.email' => '?????a ch??? email sai ?????nh d???ng',
            'password.confirmed' => 'M???t kh???u x??c nh???n kh??ng gi???ng !',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        $dataRes = $this->_shopStaffServices->crudUpdate($request, $id);
        if (!$dataRes->result) {
            return $this->_responseError($dataRes->error);

        }

        return $this->_responseSuccess('Success', 'C???p nh???t th??nh c??ng th??ng tin nh??n vi??n' ); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $you = $request->user();
        if (!$you) {
            abort(404);
        }
        $id = $request->input('id', 0);
        $staff = $this->_shopStaffInterface->getById($id);
        if ($staff) {
            $staff->delete();
        }

        //L??u log
        event(new CreateLogEvents([], 'shop_staff', 'shop_staff_delete'));

        return $this->_responseSuccess('Success', 'X??a th??nh c??ng th??ng tin nh??n vi??n' ); 
    }
}