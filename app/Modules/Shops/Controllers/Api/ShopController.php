<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Api;

use Auth;
use Validator;

use Illuminate\Http\Request;

use App\Http\Controllers\Api\AbstractApiController;

use App\Rules\PhoneRule;

use App\Modules\Operators\Models\Repositories\Contracts\PostOfficesInterface;

use App\Modules\Orders\Constants\ShopConstant;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShipAssignedInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Services\ShopServices;

use App\Modules\Systems\Models\Repositories\Contracts\NotificationInterface;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;

class ShopController extends AbstractApiController
{
    protected $_postOfficesInterface;
    protected $_notificationInterface;
    protected $_shopsInterface;
    protected $_notificationSendInterface;
    protected $_orderShipAssignedInterface;
    protected $_ordersInterface;
    protected $_shopServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PostOfficesInterface $postOfficesInterface,
                                NotificationInterface $notificationInterface,
                                NotificationSendInterface $notificationSendInterface,
                                OrderShipAssignedInterface $orderShipAssignedInterface,
                                OrdersInterface $ordersInterface,
                                ShopsInterface $shopsInterface,
                                ShopServices $shopServices)
    {
        parent::__construct();

        $this->_postOfficesInterface = $postOfficesInterface;
        $this->_notificationInterface = $notificationInterface;
        $this->_shopsInterface = $shopsInterface;
        $this->_notificationSendInterface = $notificationSendInterface;
        $this->_orderShipAssignedInterface = $orderShipAssignedInterface;
        $this->_ordersInterface = $ordersInterface;
        $this->_shopServices = $shopServices;
    }

    public function getFilterAddress(Request $request)
    {
        $response = array();
        $provinces = file_get_contents(base_path('public/address/provinces.json'));
        $districts = file_get_contents(base_path('public/address/districts.json'));
        $wards = file_get_contents(base_path('public/address/wards.json'));
        $response['provinces'] = json_decode($provinces, true);
        $response['districts'] = json_decode($districts, true);
        $response['wards'] = json_decode($wards, true);

        return $this->_responseSuccess('Success', $response);
    }

    public function dashboard(Request $request) {
        $arrData = array();
        $shop = $request->user();
        if (!$shop) {
            return $this->_responseError('Shop không tồn tại.');
        }
        $shop_id  = $shop->id;
        $date = date('Ymd');
        $orderSuccess = $this->_orderShipAssignedInterface->checkExist(array('shop_id' => $shop_id, 'user_role' => 'shipper', 'status_detail' => 23, 'time_success' => $date));
        $orderWaitShip = $this->_ordersInterface->checkExist(array('shop_id' => $shop_id, 'status_detail' => 22, 'created_date' => $date));
        $orderShipping = $this->_ordersInterface->checkExist(array('shop_id' => $shop_id, 'status_detail' => 23, 'created_date' => $date));
        $orderReShip = $this->_ordersInterface->checkExist(array('shop_id' => $shop_id, 'status_detail' => 41, 'created_date' => $date));
        $orderWaitRefund = $this->_ordersInterface->checkExist(array('shop_id' => $shop_id, 'status_detail' => 34, 'created_date' => $date));
        $orderRefund = $this->_ordersInterface->checkExist(array('shop_id' => $shop_id, 'status_detail' => 35, 'created_date' => $date));
        $orderCancel = $this->_ordersInterface->checkExist(array('shop_id' => $shop_id, 'status_detail' => 61, 'created_date' => $date));
        $orderReconcile = $this->_ordersInterface->checkExist(array('shop_id' => $shop_id, 'status' => 8, 'created_date' => $date));

        $beginDate = (int)date('Ymd', strtotime('-30 days'));
        $endDate = (int)date('Ymd');
        $unreadNotification = $this->_notificationSendInterface->checkExist(
            array(
                'shop_id' => auth()->id(),
                'date_range' => array(
                    $beginDate, $endDate
                ),
                'is_read' => 0
            )
        );

        $arrData = array(
            'unread_notification' => $unreadNotification,
            'total_success' => $orderSuccess,
            'total_waiting' => $orderWaitShip,
            'date_time' => date('Y-m-d H:i:s'),
            'time_per_call' => 300,
            'lists' => array(
                array('name' => 'Chờ giao hàng', 'value' => number_format($orderWaitShip), 'status' => 2, 'status_detail' => 22),
                array('name' => 'Giao hàng', 'value' => number_format($orderShipping), 'status' => 2, 'status_detail' => 23),
                array('name' => 'Chờ xác nhận giao lại', 'value' => number_format($orderReShip), 'status' => 4, 'status_detail' => 41),
                array('name' => 'Giao hàng thành công', 'value' => number_format($orderSuccess), 'status' => 5),
                array('name' => 'Chờ duyệt hoàn', 'value' => number_format($orderWaitRefund), 'status' => 3, 'status_detail' => 34),
                array('name' => 'Đơn duyệt hoàn', 'value' => number_format($orderRefund), 'status' => 3, 'status_detail' => 35),
                array('name' => 'Đơn hủy', 'value' => number_format($orderCancel), 'status' => 6, 'status_detail' => 61),
                array('name' => 'Đối soát', 'value' => number_format($orderReconcile), 'status' => 8)
            )
        );

        return $this->_responseSuccess('Success', $arrData);
    }

    public function update(Request $request) {
        $shop = $request->user();
        $id = $shop->id;

        $rules = [
            'name' => 'min:2|max:255',
            'stk' => 'nullable|regex:/^[0-9]+$/|max:50',
            'address' => 'min:2',
        ];

        $messages = [
            'stk.regex' => 'Số tài khoản chỉ chấp nhận ký tự số',
            'name.min' => 'Tên quá ngắn',
            'address.min' => 'Địa chỉ quá ngắn',
        ];

        if ( $request->has('addAddress') ) {
            $rules += array(
                'addPhone.*' => array(new PhoneRule()),
                'addName.*' => 'required|max:255',
                'addAddress.*' => 'required|max:255'
            );
            $messages += array(
                'address.required' => 'Phải có ít nhất 1 địa chỉ lấy hàng',
            );
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        $dataRes = $this->_shopServices->crudUpdateByPath($request, $id);
        if (!$dataRes->result) {
            return $this->_responseError($dataRes->error);
        }

        return $this->_responseSuccess('Success', 'Cập nhật thông tin Shop thành công!');
    }
}
