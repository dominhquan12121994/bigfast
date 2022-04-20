<?php

/**
 * Class IndexController
 * @package App\Modules\Orders\Controllers\Web
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Controllers\Admin;

use DB;
use Auth;
use Validator;
use App\Http\Controllers\Admin\AbstractAdminController;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;
use Illuminate\Http\Request;
use App\Modules\Systems\Services\NotificationServices;

class UserNotificationController extends AbstractAdminController
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

    public function index(Request $request) {
        $beginDate = $request->input('begin', date('d-m-Y', strtotime('-30 days')));
        $endDate = $request->input('end', date('d-m-Y'));

        $dateRange = array(
            (int)date("Ymd", strtotime($beginDate)),
            (int)date("Ymd", strtotime($endDate))
        );

        $filter = array(
            'date_range' => array($beginDate, $endDate)
        );

        $userId = Auth::guard('admin')->id();

        $conditions = array(
            'date_range' => $dateRange,
            'user_id' => $userId,
        );

        $fetchOptions = array(
            'with' => array('notification'),
            'orderBy' => array('is_read', 'notification_id'),
            'direction' => array('ASC', 'DESC'),
        );

        $arrNotification = $this->_notificationSendInterface->getMore($conditions, $fetchOptions, 10);
        $countAllNotification = $this->_notificationSendInterface->checkExist(array(
            'user_id' => $userId,
        ));

        $arrNotification->each(function ($item) {
            return $item->notification->content = $this->_notificationServices->generateContent(array(
                'content_data' => json_decode($item->notification->content_data)
            ));
        });

        return view('Orders::user-notification.list', [
            'arrNotification' => $arrNotification,
            'filter' => $filter,
            'countAllNotification' => $countAllNotification,
        ]);
    }

    public function setRead(Request $request)
    {
        try {
            $userId = auth('admin')->user()->id;

            $validator = Validator::make($request->all(), [
                'cbx_notification_id' => 'required',
                'cbx_notification_id.*' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                \Func::setToast('Thất bại', 'Đánh dấu thông báo đã đọc thất bại', 'error');
                return redirect()->route('admin.user-notification.index');
            }

            DB::beginTransaction();

            $conditions = array(
                'arr_notification' => $request->input('cbx_notification_id'),
                'user_id' => $userId,
            );

            $fillData = array(
                'is_read' => true
            );

            $result = $this->_notificationSendInterface->updateByCondition($conditions, $fillData, array(), true);

            DB::commit();

            \Func::setToast('Thành công', 'Đánh dấu thông báo đã đọc thành công', 'notice');
        } catch (Throwable $e) {
            DB::rollBack();
            $message = $e->getMessage();
            \Func::setToast('Thất bại', 'Đánh dấu thông báo đã đọc thất bại', 'error');
        }
        return redirect()->route('admin.user-notification.index');
    }
}
