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
            'shop_id.required' => 'Vui lòng chọn shop',
            'shop_id.numeric' => 'Sai thông tin shop',
            'lading_code.required' => 'Vui lòng nhập mã vận đơn',
            'lading_code.string' => 'Sai thông tin mã vận đơn',
            'value.required' => 'Vui lòng nhập giá trị tiền phát sinh',
            'value.numeric' => 'Sai thông tin giá trị tiền phát sinh',
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
                    'Sai thông tin về loại tiền và giá trị được nhập'
                ));
            }
        }

        $order = $this->_ordersInterface->getOne(array(
            'lading_code' => $request->input('lading_code'),
        ));

        if (!$order) {
            return Redirect::back()->withInput()->withErrors(array(
                'lading_code' => array('Sai thông tin mã vận đơn')
            ));
        }

        // kiểm tra đơn có phải của shop không
        if ($order->shop_id !== $request->input('shop_id')) {
            return Redirect::back()->withInput()->withErrors(array(
                'shop_id' => array('Sai thông tin shop')
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
                    'Bạn không thể thêm mới "' . $orderFeeTypes[$feeType] . '" đối với đơn hàng này!'
                ));
            }
        } else {
            return Redirect::back()->withInput()->withErrors(array(
                'Loại tiền phát sinh đã chọn không tồn tại'
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
            \Func::setToast('Thất bại', 'Thêm mới tiền phát sinh thất bại, loại tiền phát sinh đã tồn tại', 'error');
            return redirect()->route('admin.order-incurred-fee.create');
        }

        $newIncurredFee = $this->_orderFeeInterface->create($payload);

        //Thêm dữ liệu log
        $log_data[] = [
            'model' => $newIncurredFee,
        ];
        //Lưu log
        event(new CreateLogEvents($log_data, 'order_fee', 'order_fee_create'));

        if ($newIncurredFee) {
            \Func::setToast('Thành công', 'Thêm mới thành công tiền phát sinh đơn hàng ' . $request->input('lading_code'), 'notice');
        } else {
            \Func::setToast('Thất bại', 'Thêm mới tiền phát sinh thất bại', 'error');
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
            \Func::setToast('Thất bại', 'Không tồn tại phí phát sinh', 'error');
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
            'value.required' => 'Vui lòng nhập giá trị tiền phát sinh',
            'value.numeric' => 'Sai thông tin giá trị tiền phát sinh',
            'fee_type.required' => 'Vui lòng chọn loại tiền phát sinh',
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
                    'Sai thông tin về loại tiền và giá trị được nhập'
                ));
            }
        }

        $order = $this->_ordersInterface->getOne(array(
            'lading_code' => $request->input('lading_code'),
        ));

        if (!$order) {
            return Redirect::back()->withInput()->withErrors(array(
                'lading_code' => array('Sai thông tin mã vận đơn')
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
                    'Bạn không thể thêm mới "' . $orderFeeTypes[$feeType] . '" đối với đơn hàng này!'
                ));
            }
        } else {
            return Redirect::back()->withInput()->withErrors(array(
                'Loại tiền phát sinh đã chọn không tồn tại'
            ));
        }

        $result = $this->_orderFeeServices->crudUpdate($request, $id);
        if ($result) {
            \Func::setToast('Thành công', 'Cập nhật thành công thông tin tiền phát sinh', 'notice');
        } else {
            \Func::setToast('Thất bại', 'Cập nhật thất bại thông tin tiền phát sinh', 'error');
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
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $orderFee,
            ];
            //Lưu log
            event(new CreateLogEvents($log_data, 'order_fee', 'order_fee_delete'));

            $orderFee->delete();
        }
        \Func::setToast('Thành công', 'Xóa thành công thông tin tiền phát sinh', 'notice');
        return redirect()->route('admin.order-incurred-fee.index');
    }
}
