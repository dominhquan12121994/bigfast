<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Admin;

use Auth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Redirect;

use App\Http\Controllers\Admin\AbstractAdminController;

use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;

use App\Modules\Orders\Models\Services\CalculatorFeeServices;

use App\Modules\Orders\Models\Services\ShopReconcileServices;

use App\Modules\Orders\Models\Services\OrderFeeServices;

use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;

use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface;

use App\Modules\Orders\Models\Services\CashFlowServices;

use App\Modules\Orders\Constants\OrderConstant;

use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Systems\Events\CreateLogEvents;

class OrderFeeController extends AbstractAdminController
{

    protected $_calculatorFeeServices;
    protected $_shopReconcileServices;
    protected $_shopAddressInterface;
    protected $_cashFlowServices;
    protected $_orderFeeServices;
    protected $_orderFeeInterface;
    protected $_ordersInterface;
    protected $_shopsInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CalculatorFeeServices $calculatorFeeServices,
                                ShopReconcileServices $shopReconcileServices,
                                OrderFeeInterface $orderFeeInterface,
                                ShopAddressInterface $shopAddressInterface,
                                CashFlowServices $cashFlowServices,
                                OrderFeeServices $orderFeeServices,
                                OrdersInterface $ordersInterface,
                                ShopsInterface $shopsInterface)
    {
        parent::__construct();

        // $this->middleware(['auth:admin-api', 'scopes:admin']);

        $this->_calculatorFeeServices = $calculatorFeeServices;
        $this->_shopReconcileServices = $shopReconcileServices;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_cashFlowServices = $cashFlowServices;
        $this->_shopsInterface = $shopsInterface;
        $this->_orderFeeInterface = $orderFeeInterface;
        $this->_orderFeeServices = $orderFeeServices;
        $this->_ordersInterface = $ordersInterface;
    }

    public function index(Request $request) {
        $you = auth('admin')->user();
        if (!$you->can('action_order_fee_view'))
        {
            abort(403);
        }

        $orderFeeTypes = OrderConstant::order_fee_types;

        $validator = Validator::make($request->all(), [
            'shop_id' => 'nullable|numeric',
            'fee_type' => ['nullable', Rule::in(array_keys($orderFeeTypes))]
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors())->with($request->all());
        }

        $shopId = $request->input('shop_id', null);

        $conditions = array(
            'fee_type' => $request->input('fee_type', array_keys($orderFeeTypes)) ?? array_keys($orderFeeTypes)
        );

        $shopInfo = false;
        if ($shopId) {
            $shopInfo = $this->_shopsInterface->getById($shopId);
            if (!$shopInfo) {
                return Redirect::back()->withInput()->with($request->all());
            } else {
                $conditions['shop_id'] = $shopId;
            }
        }

        $fetchOptions = array(
            'orderBy' => 'date',
            'direction' => 'DESC',
            'with' => array(
                'shop',
                'order'
            )
        );

        $arrOrderFee = $this->_orderFeeInterface->getMore($conditions, $fetchOptions, 10);

        return view('Orders::order-incurred-fee.list', [
            'shopInfo' => $shopInfo,
            'arrOrderFee' => $arrOrderFee,
            'orderFeeTypes' => $orderFeeTypes,
            'conditions' => $conditions
        ]);
    }

    public function create()
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_fee_create'))
        {
            abort(403);
        }

        $orderFeeTypes = OrderConstant::order_fee_types;

        return view('Orders::order-incurred-fee.create', [
            'orderFeeTypes' => $orderFeeTypes
        ]);
    }

    public function store(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_fee_create'))
        {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'shop_id' => 'required|numeric',
            'lading_code' => 'required|string',
            'value' => 'required|numeric',
        ],
        [
            'shop_id.required' => 'Vui l??ng ch???n shop',
            'shop_id.numeric' => 'Sai th??ng tin shop',
            'lading_code.required' => 'Vui l??ng nh???p m?? v???n ????n',
            'lading_code.string' => 'Sai th??ng tin m?? v???n ????n',
            'value.required' => 'Vui l??ng nh???p gi?? tr??? ti???n ph??t sinh',
            'value.numeric' => 'Sai th??ng tin gi?? tr??? ti???n ph??t sinh',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $allowNegativeTypes = array(
            'incurred_total_cod',
            'incurred_fee_transport'
        );

        if ($request->input('value') < 0) {
            if (!in_array($request->input('fee_type'), $allowNegativeTypes)) {
                return Redirect::back()->withInput()->withErrors(array(
                    'Sai th??ng tin v??? lo???i ti???n v?? gi?? tr??? ???????c nh???p'
                ));
            }
        }

        $order = $this->_ordersInterface->getOne(array(
            'lading_code' => $request->input('lading_code'),
        ));

        if (!$order) {
            return Redirect::back()->withInput()->withErrors(array(
                'lading_code' => array('Sai th??ng tin m?? v???n ????n')
            ));
        }

        // ki???m tra ????n c?? ph???i c???a shop kh??ng
        if ($order->shop_id !== $request->input('shop_id')) {
            return Redirect::back()->withInput()->withErrors(array(
                'shop_id' => array('Sai th??ng tin shop')
            ));
        }

        $feeType = $request->input('fee_type');
        $incurredFeeByStatus = OrderConstant::incurred_fee_by_status;

        if (isset($incurredFeeByStatus[$feeType])) {
            $allowStatus = $incurredFeeByStatus[$feeType];
            $orderStatus = $order->status_detail;

            if (!in_array($orderStatus, $allowStatus)) {
                $orderFeeTypes = OrderConstant::order_fee_types;
                return Redirect::back()->withInput()->withErrors(array(
                    'B???n kh??ng th??? th??m m???i "' . $orderFeeTypes[$feeType] . '" ?????i v???i ????n h??ng n??y!'
                ));
            }
        } else {
            return Redirect::back()->withInput()->withErrors(array(
                'Lo???i ti???n ph??t sinh ???? ch???n kh??ng t???n t???i'
            ));
        }

        $payload = array(
            'shop_id' => $request->input('shop_id'),
            'fee_type' => $request->input('fee_type'),
            'order_id' => $order->id,
            'date' => (int)date('Ymd', strtotime($request->input('date'))),
            'value' => $request->input('value'),
        );

        $checkExist = $this->_orderFeeInterface->checkExist(array(
            'fee_type' => $request->input('fee_type'),
            'order_id' => $order->id,
        ));

        if ($checkExist) {
            \Func::setToast('Th???t b???i', 'Th??m m???i ti???n ph??t sinh th???t b???i, lo???i ti???n ph??t sinh ???? t???n t???i', 'error');
            return redirect()->route('admin.order-incurred-fee.create');
        }

        $newIncurredFee = $this->_orderFeeInterface->create($payload);

        //Th??m d??? li???u log
        $log_data[] = [
            'model' => $newIncurredFee,
        ];
        //L??u log
        event(new CreateLogEvents($log_data, 'order_fee', 'order_fee_create'));

        if ($newIncurredFee) {
            \Func::setToast('Th??nh c??ng', 'Th??m m???i th??nh c??ng ti???n ph??t sinh ????n h??ng ' . $request->input('lading_code'), 'notice');
        } else {
            \Func::setToast('Th???t b???i', 'Th??m m???i ti???n ph??t sinh th???t b???i', 'error');
        }

        return redirect()->route('admin.order-incurred-fee.create');
    }

    public function edit(Request $request, $id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_fee_update'))
        {
            abort(403);
        }

        $orderFeeTypes = OrderConstant::order_fee_types;

        $orderFee = $this->_orderFeeInterface->getOne(array(
            'id' => $id
        ), array(
            'with' => array(
                'order',
                'shop'
            )
        ));

        if (!$orderFee) {
            \Func::setToast('Th???t b???i', 'Kh??ng t???n t???i ph?? ph??t sinh', 'error');
            return redirect()->back();
        }

        return view('Orders::order-incurred-fee.edit', [
            'orderFeeTypes' => $orderFeeTypes,
            'orderFee' => $orderFee,
        ]);
    }

    public function update(Request $request, $id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_fee_update'))
        {
            abort(403);
        }

        $orderFeeTypes = OrderConstant::order_fee_types;

        $validator = Validator::make($request->all(), [
            'value' => 'required|numeric',
            'fee_type' => ['required', Rule::in(array_keys($orderFeeTypes))]
        ],
        [
            'value.required' => 'Vui l??ng nh???p gi?? tr??? ti???n ph??t sinh',
            'value.numeric' => 'Sai th??ng tin gi?? tr??? ti???n ph??t sinh',
            'fee_type.required' => 'Vui l??ng ch???n lo???i ti???n ph??t sinh',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $allowNegativeTypes = array(
            'incurred_total_cod',
            'incurred_fee_transport'
        );

        if ($request->input('value') < 0) {
            if (!in_array($request->input('fee_type'), $allowNegativeTypes)) {
                return Redirect::back()->withInput()->withErrors(array(
                    'Sai th??ng tin v??? lo???i ti???n v?? gi?? tr??? ???????c nh???p'
                ));
            }
        }

        $order = $this->_ordersInterface->getOne(array(
            'lading_code' => $request->input('lading_code'),
        ));

        if (!$order) {
            return Redirect::back()->withInput()->withErrors(array(
                'lading_code' => array('Sai th??ng tin m?? v???n ????n')
            ));
        }

        $feeType = $request->input('fee_type');
        $incurredFeeByStatus = OrderConstant::incurred_fee_by_status;

        if (isset($incurredFeeByStatus[$feeType])) {
            $allowStatus = $incurredFeeByStatus[$feeType];
            $orderStatus = $order->status_detail;

            if (!in_array($orderStatus, $allowStatus)) {
                $orderFeeTypes = OrderConstant::order_fee_types;
                return Redirect::back()->withInput()->withErrors(array(
                    'B???n kh??ng th??? th??m m???i "' . $orderFeeTypes[$feeType] . '" ?????i v???i ????n h??ng n??y!'
                ));
            }
        } else {
            return Redirect::back()->withInput()->withErrors(array(
                'Lo???i ti???n ph??t sinh ???? ch???n kh??ng t???n t???i'
            ));
        }

        $result = $this->_orderFeeServices->crudUpdate($request, $id);
        if ($result) {
            \Func::setToast('Th??nh c??ng', 'C???p nh???t th??nh c??ng th??ng tin ti???n ph??t sinh', 'notice');
        } else {
            \Func::setToast('Th???t b???i', 'C???p nh???t th???t b???i th??ng tin ti???n ph??t sinh', 'error');
        }

        return redirect()->route('admin.order-incurred-fee.index');
    }

    public function destroy($id)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_order_fee_delete'))
        {
            abort(403);
        }

        $orderFee = $this->_orderFeeInterface->getById($id);
        if ($orderFee) {
            //Th??m d??? li???u log
            $log_data[] = [
                'old_data' => $orderFee,
            ];
            //L??u log
            event(new CreateLogEvents($log_data, 'order_fee', 'order_fee_delete'));

            $orderFee->delete();
        }
        \Func::setToast('Th??nh c??ng', 'X??a th??nh c??ng th??ng tin ti???n ph??t sinh', 'notice');
        return redirect()->route('admin.order-incurred-fee.index');
    }
}
