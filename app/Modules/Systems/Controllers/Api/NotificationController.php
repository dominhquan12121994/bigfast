<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AbstractApiController;
use App\Modules\Systems\Models\Repositories\Contracts\UsersInterface;
use App\Modules\Systems\Resources\NotificationResource;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Systems\Services\NotificationServices;

class NotificationController extends AbstractApiController
{

    protected $_usersInterface;
    protected $_notificationSendInterface;
    protected $_shopsInterface;
    protected $_notificationServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UsersInterface $usersInterface,
                                ShopsInterface $shopsInterface,
                                NotificationServices $notificationServices,
                                NotificationSendInterface $notificationSendInterface)
    {
        parent::__construct();

        $this->_usersInterface = $usersInterface;
        $this->_notificationSendInterface = $notificationSendInterface;
        $this->_shopsInterface = $shopsInterface;
        $this->_notificationServices = $notificationServices;
    }

    /**
     * Display a listing by provinces.
     *
     * @return \Illuminate\Http\Response
     */
    public function getByShop(Request $request)
    {
        $shopId = auth()->id();

        $conditions = array(
            'with' => array('notification'),
            'shop_id' => $shopId,
        );

        $fetchOptions = array(
            'orderBy' => array('is_read', 'notification_id'),
            'direction' => array('ASC', 'DESC'),
        );

        $arrNotification = $this->_notificationSendInterface->customPaginate($conditions, $fetchOptions, 15);

        if (!$arrNotification) {
            return $this->_responseSuccess('Không có thông báo cho shop');
        }

        return $this->_responseSuccess('Success', new NotificationResource($arrNotification));
    }

    public function readByShop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'arr_id.*' => 'required|exists:system_notification_send,id'
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Không tìm thấy thông báo, không thể thay đổi trạng thái');
        }

        $shopId = auth()->id();

        $arrId = $request->input('arr_id');

        $result = $this->_notificationSendInterface->updateByCondition(array(
            'arr_notification' => $arrId,
            'shop_id' => $shopId
        ), array(
            'is_read' => 1
        ), array(), true);

        if (!$result) {
            return $this->_responseSuccess('Không tìm thấy thông báo, không thể thay đổi trạng thái');
        }

        return $this->_responseSuccess('Thay đổi trạng thái đã đọc thông báo thành công');
    }

    public function deleteByShop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'arr_id.*' => 'required|exists:system_notification_send,id'
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Không tìm thấy thông báo');
        }

        $shopId = auth()->id();

        $arrId = $request->input('arr_id');

        $result = $this->_notificationSendInterface->updateByCondition(array(
            'arr_notification' => $arrId,
            'shop_id' => $shopId,
        ), array(
            'deleted_at' => date('Y-m-d H:i:s')
        ), array(), true);

        if (!$result) {
            return $this->_responseSuccess('Không tìm thấy thông báo, xoá thông báo thất bại');
        }

        return $this->_responseSuccess('Xoá thông báo thành công');
    }

    public function getByShipper(Request $request)
    {
        $userId = auth()->id();

        $userRole = auth()->user()->getRoleNames()[0];

        $arrRole = array(
            "shipper", "pickup"
        );

        if (!in_array($userRole, $arrRole) ) {
            return $this->_responseError('Vai trò không khả dụng');
        }

        $conditions = array(
            'with' => array('notification'),
            'user_id' => $userId,
        );

        $fetchOptions = array(
            'orderBy' => array('is_read', 'notification_id'),
            'direction' => array('ASC', 'DESC'),
        );

        $arrNotification = $this->_notificationSendInterface->customPaginate($conditions, $fetchOptions, 15);

        $arrNotification['data']->each(function ($item) {
            return $item->notification->content = $this->_notificationServices->generateContent(array(
                'content_data' => json_decode($item->notification->content_data)
            ));
        });

        if (!$arrNotification) {
            return $this->_responseSuccess('Không có thông báo cho shipper');
        }

        return $this->_responseSuccess('Success', new NotificationResource($arrNotification));
    }

    public function readByShipper(Request $request)
    {
        $userRole = auth()->user()->getRoleNames()[0];

        $arrRole = array(
            "shipper", "pickup"
        );

        if (!in_array($userRole, $arrRole) ) {
            return $this->_responseError('Vai trò không khả dụng');
        }

        $validator = Validator::make($request->all(), [
            'arr_id.*' => 'required|exists:system_notification_send,id'
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Không tìm thấy thông báo, không thể thay đổi trạng thái');
        }

        $userId = auth()->id();

        $arrId = $request->input('arr_id');

        $result = $this->_notificationSendInterface->updateByCondition(array(
            'arr_notification' => $arrId,
            'user_id' => $userId,
        ), array(
            'is_read' => 1
        ), array(), true);

        if (!$result) {
            return $this->_responseSuccess('Thay đổi trạng thái đã đọc thông báo thất bại');
        }

        return $this->_responseSuccess('Thay đổi trạng thái đã đọc thông báo thành công');
    }

    public function deleteByShipper(Request $request)
    {
        $userRole = auth()->user()->getRoleNames()[0];

        $arrRole = array(
            "shipper", "pickup"
        );

        if (!in_array($userRole, $arrRole) ) {
            return $this->_responseError('Vai trò không khả dụng');
        }

        $validator = Validator::make($request->all(), [
            'arr_id.*' => 'required|exists:system_notification_send,id'
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Không tìm thấy thông báo');
        }

        $userId = auth()->id();

        $arrId = $request->input('arr_id');

        $result = $this->_notificationSendInterface->updateByCondition(array(
            'arr_notification' => $arrId,
            'user_id' => $userId,
        ), array(
            'deleted_at' => date('Y-m-d H:i:s')
        ), array(), true);

        if (!$result) {
            return $this->_responseSuccess('Xoá thông báo thất bại');
        }

        return $this->_responseSuccess('Xoá thông báo thành công');
    }
}
