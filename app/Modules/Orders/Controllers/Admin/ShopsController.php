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
use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;

use App\Modules\Orders\Models\Services\ShopServices;
use App\Modules\Systems\Events\CreateLogEvents;
use App\Rules\PasswordRule;

class ShopsController extends AbstractAdminController
{

    protected $_shopsInterface;
    protected $_shopBankInterface;
    protected $_shopStaffInterface;
    protected $_shopAddressInterface;
    protected $_orderServiceInterface;
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
                                ShopStaffInterface $shopStaffInterface,
                                ShopAddressInterface $shopAddressInterface,
                                OrderServiceInterface $orderServiceInterface,
                                ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                WardsInterface $wardsInterface,
                                ShopServices $shopServices)
    {
        parent::__construct();

        $this->_shopsInterface = $shopsInterface;
        $this->_shopBankInterface = $shopBankInterface;
        $this->_shopStaffInterface = $shopStaffInterface;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_orderServiceInterface = $orderServiceInterface;
        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_wardsInterface = $wardsInterface;
        $this->_shopServices = $shopServices;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_shops_view'))
        {
            abort(403);
        }

        $search = '';
        $filter = array();
        if ($request->has('search')) {
            $search = $request->input('search');
            $filter['name'] = $search;
            $filter['phone'] = $search;
            $filter['email'] = $search;
        }

        $shops = $this->_shopsInterface->getMore($filter, array('with' => array('staff', 'bank')), 10);

        return view('Orders::shops.list', [
            'shops' => $shops,
            'search' => $search
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_shops_create'))
        {
            abort(403);
        }

        $provinces = $this->_provincesInterface->getMore();
        $districts = $this->_districtsInterface->getMore(array('p_id' => $provinces[0]->id));
        $wards = $this->_wardsInterface->getMore(array('d_id' => $districts[0]->id));

        return view('Orders::shops.create', [
            'cycleCodList' => ShopConstant::bank['cycle_cod'],
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards
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
        $you = auth('admin')->user();
        if (!$you->can('action_shops_create'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'phone' => array('required', 'unique:order_shops', new PhoneRule()),
            'email' => 'required|email|unique:order_shops|max:255',
            'name' => 'required|max:255',
            'password' => array('required', 'confirmed', new PasswordRule() ),
            'stk' => 'nullable|regex:/^[0-9]+$/|max:50',
            'address' => array('required', new ExceptSpecialCharRule()),
            'bank_name' => array('required', new ExceptSpecialCharRule()),
            'bank_branch' => array('required', new ExceptSpecialCharRule()),
            'stk_name' => array('required', new ExceptSpecialCharRule()),
            'addName' => 'required|min:1',
            'addName.*' => array('required', new ExceptSpecialCharRule()),
            'addPhone.*' => array('required', new PhoneRule()),
            'addName.*' => 'required|max:255',
            'addAddress.*' => array('required', 'max:255', new ExceptSpecialCharRule())
        ],
        [
            'addName.required' => 'Phải có ít nhất 1 địa chỉ lấy hàng',
            'name.unique' => 'Tên shop đã tồn tại',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'email.unique' => 'Địa chỉ email đã tồn tại',
            'email.email' => 'Địa chỉ email sai định dạng',
            'password.confirmed' => 'Xác nhận mật khẩu không đúng',
            'stk.regex' => 'Số tài khoản chỉ chấp nhận ký tự số',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }

        $dataRes = $this->_shopServices->crudStore($request);
        if (!$dataRes->result) {
            return redirect()->back()->withInput()->withErrors($dataRes->error);
        }

        \Func::setToast('Thành công', 'Thêm mới thành công Shop: ' . $request->input('name'), 'notice');
        return redirect()->route('admin.shops.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_shops_view'))
        {
            abort(403);
        }

        $shop = $this->_shopsInterface->getById($id);

        return view('Orders::shops.show', ['shop' => $shop]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_shops_update'))
        {
            abort(403);
        }

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

        $serviceList = array();
        $orderServices = $this->_orderServiceInterface->getMore();
        if (count($orderServices) > 0) {
            foreach ($orderServices as $key => $service) {
                $serviceList[$service->alias] = $service->name;
            }
        }

        $provinces = $this->_provincesInterface->getMore();
        $districts = $this->_districtsInterface->getMore(array('p_id' => $provinces[0]->id));
        $wards = $this->_wardsInterface->getMore(array('d_id' => $districts[0]->id));

        return view('Orders::shops.edit', [
            'shop' => $shop,
            'shopBank' => $shopBank,
            'shopAddress' => $shopAddress,
            'provinces' => $provinces,
            'districts' => $districts,
            'wards' => $wards,
            'addWards' => $addWards,
            'addDistricts' => $addDistricts,
            'serviceList' => $serviceList,
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
    public function update(Request $request, $id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_shops_update'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'phone' => array('required', 'unique:order_shops,phone,'. $id, new PhoneRule()),
            'email' => 'required|email|unique:order_shops,email,' . $id,
            'name' => 'required|max:255',
            'stk' => 'nullable|regex:/^[0-9]+$/|max:50',
            'address' => array('required', new ExceptSpecialCharRule()),
            'bank_name' => array('required', new ExceptSpecialCharRule()),
            'bank_branch' => array('required', new ExceptSpecialCharRule()),
            'stk_name' => array('required', new ExceptSpecialCharRule()),
            'addName' => 'required|min:1',
            'addName.*' => array('required', new ExceptSpecialCharRule()),
            'addPhone.*' => array('required', new PhoneRule()),
            'addName.*' => 'required|max:255',
            'addAddress.*' => array('required', 'max:255', new ExceptSpecialCharRule())
        ], [
            'addName.required' => 'Phải có ít nhất 1 địa chỉ lấy hàng',
            'name.unique' => 'Tên shop đã tồn tại',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'email.unique' => 'Địa chỉ email đã tồn tại',
            'email.email' => 'Địa chỉ email sai định dạng',
            'stk.regex' => 'Số tài khoản chỉ chấp nhận ký tự số',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors());
        }

        $dataRes = $this->_shopServices->crudUpdate($request, $id);
        if (!$dataRes->result) {
            return redirect()->back()->withInput()->withErrors($dataRes->error);
        }

        \Func::setToast('Thành công', 'Cập nhật thành công thông tin Shop', 'notice');

        if ($request->has('create-order')) {
            return redirect()->route('admin.orders.create', array('shop_id' => $id));
        }
        return redirect()->route('admin.shops.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_shops_delete'))
        {
            abort(403);
        }

        $shop = $this->_shopsInterface->getById($id);
        if ($shop) {
            $shop->delete();
        }

        //Lưu log
        event(new CreateLogEvents([], 'shops', 'shops_delete'));

        return redirect()->route('admin.shops.index');
    }
}
