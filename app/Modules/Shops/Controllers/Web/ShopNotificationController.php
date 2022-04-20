<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web;

use Auth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Redirect;
use DB;
use App\Http\Controllers\Web\AbstractWebController;
use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;
use App\Modules\Orders\Models\Services\CalculatorFeeServices;
use App\Modules\Orders\Models\Services\ShopReconcileServices;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Services\CashFlowServices;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;
use App\Modules\Systems\Services\NotificationServices;

class ShopNotificationController extends AbstractWebController
{

    protected $_calculatorFeeServices;
    protected $_shopReconcileServices;
    protected $_shopAddressInterface;
    protected $_cashFlowServices;
    protected $_shopsInterface;
    protected $_notificationSendInterface;
    protected $_notificationServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CalculatorFeeServices $calculatorFeeServices,
                                ShopReconcileServices $shopReconcileServices,
                                ShopAddressInterface $shopAddressInterface,
                                NotificationServices $notificationServices,
                                NotificationSendInterface $notificationSendInterface,
                                CashFlowServices $cashFlowServices,
                                ShopsInterface $shopsInterface)
    {
        parent::__construct();

        $this->_calculatorFeeServices = $calculatorFeeServices;
        $this->_shopReconcileServices = $shopReconcileServices;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_cashFlowServices = $cashFlowServices;
        $this->_shopsInterface = $shopsInterface;
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

        $shopId = Auth::guard('shop')->id();

        $conditions = array(
            'with' => array('notification.user'),
            'date_range' => $dateRange,
            'shop_id' => $shopId,
        );

        $fetchOptions = array(
            'orderBy' => array('is_read', 'notification_id'),
            'direction' => array('ASC', 'DESC'),
        );

        $arrNotification = $this->_notificationSendInterface->getMore($conditions, $fetchOptions, 10);
        $countAllNotification = $this->_notificationSendInterface->checkExist(array(
            'shop_id' => $shopId,
        ));

        $arrNotification->each(function ($item) {
            return $item->notification->content = $this->_notificationServices->generateContent(array(
                'content_data' => json_decode($item->notification->content_data)
            ));
        });

        return view('Shops::shop-notification.list', [
            'arrNotification' => $arrNotification,
            'shopId' => $shopId,
            'filter' => $filter,
            'countAllNotification' => $countAllNotification,
        ]);
    }

    public function saveReadNotification(Request $request)
    {
        $shopId = auth('shop')->user()->id;

        $validator = Validator::make($request->all(), [
            'notification_id' => 'exists:system_notification_send,id',
        ]);

        if ($validator->fails()) {
            \Func::setToast('Thất bại', 'Đánh dấu thông báo đã đọc thất bại', 'error');
            return redirect()->route('shop.shop-notification.index');
        }

        $conditions = array(
            'arr_notification' => $request->input('cbx_notification_id'),
        );

        $fillData = array(
            'is_read' => true
        );

        $result = $this->_notificationSendInterface->updateByCondition($conditions, $fillData, array(), true);

        if ($result) {
            \Func::setToast('Thành công', 'Đánh dấu thông báo đã đọc thành công', 'notice');
        } else {
            \Func::setToast('Thất bại', 'Đánh dấu thông báo đã đọc thất bại', 'error');
        }

        return redirect()->route('shop.shop-notification.index');
    }
}
