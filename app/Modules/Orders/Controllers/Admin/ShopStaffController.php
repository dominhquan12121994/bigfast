<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Admin;

use Validator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Rules\PhoneRule;
use App\Rules\ExceptSpecialCharRule;
use App\Http\Controllers\Admin\AbstractAdminController;
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

class ShopStaffController extends AbstractAdminController
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
    public function index(Request $request, $shop_id)
    {
        $search = '';
        $filter = array('shop_id' => $shop_id);
        if ($request->has('search')) {
            $search = $request->input('search');
            $filter['name'] = $search;
            $filter['phone'] = $search;
            $filter['email'] = $search;
        }

        $shop = $this->_shopsInterface->getById($shop_id);
        if (!$shop) {
            abort(404);
        }

        $staff = $this->_shopStaffInterface->getMore($filter, array(), 10);

        return view('Orders::shop-staff.list', [
            'shop' => $shop,
            'staffs' => $staff,
            'search' => $search
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if (!$request->has('shop_id')) {
            abort(404);
        }

        $shop = $this->_shopsInterface->getById($request->input('shop_id'));
        if (!$shop) {
            abort(404);
        }

        return view('Orders::shop-staff.create', [
            'shop' => $shop
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->has('shop_id')) {
            abort(404);
        }

        $shop = $this->_shopsInterface->getById($request->input('shop_id'));
        if (!$shop) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'phone' => array('required', 'unique:order_shops_staff', new PhoneRule()),
            'email' => 'required|email|unique:order_shops_staff|max:255',
            'name' => array('required', 'max:255', new ExceptSpecialCharRule()),
            'password' => array('required', 'confirmed', new PasswordRule() )
        ],
        [
            'name.unique' => 'T??n shop ???? t???n t???i',
            'phone.unique' => 'S??? ??i???n tho???i ???? t???n t???i',
            'email.unique' => '?????a ch??? email ???? t???n t???i',
            'email.email' => '?????a ch??? email sai ?????nh d???ng',
            'password.confirmed' => 'X??c nh???n m???t kh???u kh??ng ????ng',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $dataRes = $this->_shopStaffServices->crudStore($request);
        if (!$dataRes->result) {
            return redirect()->back()->withInput()->withErrors($dataRes->error);
        }

        \Func::setToast('Th??nh c??ng', 'Th??m m???i th??nh c??ng nh??n vi??n: ' . $request->input('name'), 'notice');
        return redirect()->route('admin.shop-staff.index', array('shop_id' => $shop->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $staff = $this->_shopStaffInterface->getById($id);

        return view('Orders::shop-staff.show', ['staff' => $staff]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $staff = $this->_shopStaffInterface->getById($id);
        if (!$staff) {
            abort(404);
        }

        return view('Orders::shop-staff.edit', [
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
        $staff = $this->_shopStaffInterface->getById($id);
        if (!$staff) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'phone' => array('required', 'unique:order_shops_staff,phone,'. $id, new PhoneRule()),
            'email' => 'required|email|unique:order_shops_staff,email,' . $id,
            'password' => array('string', 'confirmed', new PasswordRule),
            'name' => array('required', 'max:255', new ExceptSpecialCharRule())
        ], [
            'name.unique' => 'T??n shop ???? t???n t???i',
            'phone.unique' => 'S??? ??i???n tho???i ???? t???n t???i',
            'email.unique' => '?????a ch??? email ???? t???n t???i',
            'email.email' => '?????a ch??? email sai ?????nh d???ng',
            'password.confirmed' => 'M???t kh???u x??c nh???n kh??ng gi???ng !',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $dataRes = $this->_shopStaffServices->crudUpdate($request, $id);
        if (!$dataRes->result) {
            return redirect()->back()->withInput()->withErrors($dataRes->error);
        }

        \Func::setToast('Th??nh c??ng', 'C???p nh???t th??nh c??ng th??ng tin nh??n vi??n', 'notice');

        return redirect()->route('admin.shop-staff.index', array('shop_id' => $staff->shop_id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $staff = $this->_shopStaffInterface->getById($id);
        $shop_id = $staff->shop_id;
        if ($staff) {
            $staff->delete();
        }

        //L??u log
        event(new CreateLogEvents([], 'shop_staff', 'shop_staff_delete'));

        return redirect()->route('admin.shop-staff.index', array('shop_id' => $shop_id));
    }
}
