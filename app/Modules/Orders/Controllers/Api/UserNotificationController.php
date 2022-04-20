<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Api;

use DB;
use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AbstractApiController;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;

class UserNotificationController extends AbstractApiController
{
    protected $_notificationSendInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(NotificationSendInterface $notificationSendInterface)
    {
        parent::__construct();

        $this->_notificationSendInterface = $notificationSendInterface;
    }

    public function setRead(Request $request)
    {
        $userId = $request->user()->id;

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
            'user_id' => $userId,
        );

        $fillData = array(
            'is_read' => true
        );

        $this->_notificationSendInterface->updateByCondition($conditions, $fillData, array(), true);

        return $this->_responseSuccess('Thay đổi trạng thái thông báo thành công');
    }
}
