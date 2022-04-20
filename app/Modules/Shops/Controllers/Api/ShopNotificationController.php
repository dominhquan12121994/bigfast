<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Api;

use Auth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Http\Controllers\Api\AbstractApiController;

use App\Modules\Shops\Resources\ShopNotificationResource;

use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;
use App\Modules\Systems\Services\NotificationServices;
use App\Modules\Systems\Constants\NotificationConstant;

class ShopNotificationController extends AbstractApiController
{
    protected $_notificationSendInterface;
    protected $_notificationServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(NotificationSendInterface $notificationSendInterface,
                                NotificationServices $notificationServices)
    {
        parent::__construct();

        $this->_notificationSendInterface = $notificationSendInterface;
        $this->_notificationServices = $notificationServices;
    }

    public function updateNotificationRead(Request $request)
    {
        $request->merge(array(
            'shop_id' => $request->user()->id
        ));

        $validator = Validator::make($request->all(), [
            'arr_notification_id' => 'required',
            'arr_notification_id.*' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Dữ liệu sai, không thể cập nhật trạng thái thông báo');
        }

        $arrNotificationId = $request->input('arr_notification_id');

        $conditions = array(
            'arr_notification_id' => $arrNotificationId,
            'shop_id' => $request->user()->id,
        );

        $fillData = array(
            'is_read' => true
        );

        $this->_notificationSendInterface->updateByCondition($conditions, $fillData, array(), true);

        return $this->_responseSuccess('Thay đổi trạng thái thông báo thành công');
    }

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_read' => 'required|in:-1,0,1',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $beginDate = (int)date('Ymd', strtotime('-30 days'));
        $endDate = (int)date('Ymd');

        if ($request->input('is_read') === -1) {
            $condition = array(
                'shop_id' => auth()->id(),
                'date_range' => array(
                    $beginDate, $endDate
                ),
            );
        } else {
            $condition = array(
                'shop_id' => auth()->id(),
                'date_range' => array(
                    $beginDate, $endDate
                ),
                'is_read' => $request->input('is_read')
            );
        }

        $notificationsSend = $this->_notificationSendInterface->getMore(
            $condition,
            array(
                'with' => array('notification.user'),
                'orderBy' => array(
                    'id'
                ),
                'direction' => array(
                    'DESC'
                ),
            )
        );

        if (empty($notificationsSend)) {
            return $this->_responseSuccess('Success', array());
        }

        $notificationsSend->each(function ($item) {
            return $item->notification->content = $this->_notificationServices->generateContent(array(
                'content_data' => json_decode($item->notification->content_data)
            ));
        });

        return $this->_responseSuccess('Success', new ShopNotificationResource($notificationsSend));
    }

    public function read(Request $request) {
        $validator = Validator::make($request->all(), [
            'arr_id' => 'required',
            'arr_id.*' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $arrNotificationId = $request->input('arr_id');
        $shopId = auth()->id();

        $result = $this->_notificationSendInterface->updateByCondition(
            array(
                'arr_notification_id' => $arrNotificationId,
                'shop_id' => $shopId,
            ),
            array(
                'is_read' => 1
            ),
            array(),
            true
        );

        if ($result) {
            return $this->_responseSuccess('Success', 'Thay đổi trạng thái thông báo thành đã đọc thành công');
        }

        return $this->_responseSuccess('Success', 'Thay đổi trạng thái thông báo thành đã đọc thất bại');
    }

    public function delete(Request $request) {
        $validator = Validator::make($request->all(), [
            'arr_id' => 'required',
            'arr_id.*' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $arrNotificationId = $request->input('arr_id');
        $shopId = auth()->id();

        $result = $this->_notificationSendInterface->delByCond(
            array(
                'arr_notification_id' => $arrNotificationId,
                'shop_id' => $shopId
            ),
            true
        );

        if ($result) {
            return $this->_responseSuccess('Success', 'Xóa thông báo thành công');
        }

        return $this->_responseSuccess('Success', 'Xóa thông báo thất bại');
    }
}
