<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Api\AbstractApiController;

use App\Modules\Orders\Models\Entities\OrderShipAssigned;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShipAssignedInterface;

use App\Modules\Orders\Resources\OrderShipperResource;
use App\Modules\Orders\Constants\OrderConstant;

class ShipperController extends AbstractApiController
{

    protected $_orderShipAssignedInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrderShipAssignedInterface $orderShipAssignedInterface)
    {
        parent::__construct();

        $this->_orderShipAssignedInterface = $orderShipAssignedInterface;
    }

    public function dashboard(Request $request)
    {
        $arrData = array();
        $arrRole = array('pickup', 'shipper', 'refund');
        $user = Auth::guard('admin-api')->user();
        $user_roles = $user->getRoleNames()->toArray();
        $user_role = $user->getRoleNames()[0];
        $user_id = (int)$user->id;

        if (in_array($user_role, $arrRole)) {

            if (in_array('shipper', $user_roles)) {
                $collect = 0;
                $assigned = $this->_orderShipAssignedInterface->getMore(
                    array(
                        'user_id' => $user_id,
                        'user_role' => $user_role,
                        'status_detail' => 23,
                        'time_success' => (int)date('Ymd')
                    ),
                    array(
                        'with' => array('order')
                    ));

                if (count($assigned) > 0) {
                    foreach ($assigned as $assign) {
                        $order = $assign->order;
                        $collect += $order->cod + (($order->payfee === 'payfee_receiver') ? $order->transport_fee : 0);
                    }
                }

                $orderSuccess       = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'shipper', 'status_detail' => 23, 'time_success' => (int)date('Ymd')));
                $orderNeedSend      = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'shipper', 'status_detail' => 23, 'time_success' => 0, 'time_failed' => 0));
                $orderAssigned      = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'shipper', 'status_detail' => 23, 'time_assigned' => (int)date('Ymd')));
                $orderFailed        = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'shipper', 'status_detail' => 23, 'time_failed' => (int)date('Ymd'), 'failed_status' => 41));
                $orderFailedMissing = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'shipper', 'status_detail' => 23, 'time_failed' => (int)date('Ymd'), 'failed_status' => 71));
                // $orderFailedDamaged = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'shipper', 'status_detail' => 23, 'time_failed' => (int)date('Ymd'), 'failed_status' => 72));
                $orderFailedConfirm = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'shipper', 'status_detail' => 23, 'time_failed' => (int)date('Ymd'), 'failed_status' => 34));
                $orderFailedRefund  = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'shipper', 'status_detail' => 23, 'time_failed' => (int)date('Ymd'), 'failed_status' => 35));

                $arrData += array(
                    'success' => number_format($orderSuccess),
                    'collect' => number_format($collect),
                    'reports' => array(
                        array('name' => 'Tổng đơn cần giao', 'value' => number_format($orderNeedSend)),
                        array('name' => 'Tổng đơn nhận', 'value' => number_format($orderAssigned)),
                        array('name' => 'Chờ xác nhận giao lại', 'value' => number_format($orderFailed)),
                        array('name' => 'Thất lạc', 'value' => number_format($orderFailedMissing)),
                        array('name' => 'Chờ duyệt hoàn', 'value' => number_format($orderFailedConfirm)),
                        array('name' => 'Đang hoàn về kho', 'value' => number_format($orderFailedRefund))
                    )
                );
            }

            if (in_array('pickup', $user_roles)) {
                $warehouse          = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'pickup', 'status_detail' => 12, 'time_success' => (int)date('Ymd')));
                $orderNeedPick      = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'pickup', 'status_detail' => 12, 'time_success' => 0, 'time_failed' => 0));
                $orderFailedPick    = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'pickup', 'status_detail' => 12, 'time_failed' => (int)date('Ymd')));
                $orderCancelPick    = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'pickup', 'status_detail' => 12, 'onlyTrashed' => true, 'deleted_at' => date('Y-m-d') ));

                $arrData += array(
                    'warehouse' => number_format($warehouse),
                    'report_pickup' => array(
                        array('name' => 'Tổng đơn cần lấy', 'value' => number_format($orderNeedPick)),
                        array('name' => 'Lấy không thành công', 'value' => number_format($orderFailedPick)),
                        array('name' => 'Đơn huỷ', 'value' => number_format($orderCancelPick)),
                    ),
                );
            }

            if (in_array('refund', $user_roles)) {
                $refund                         = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'refund', 'status_detail' => 32, 'time_success' => (int)date('Ymd')));
                $orderNeedRefund                = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'refund', 'status_detail' => 32, 'time_success' => 0, 'time_failed' => 0));
                $orderFailedRefundMissing       = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'refund', 'status_detail' => 32, 'time_failed' => (int)date('Ymd'), 'failed_status' => 71));
                $orderFailedRefundReconcile     = $this->_orderShipAssignedInterface->checkExist(array('user_id' => $user_id, 'user_role' => 'refund', 'status_detail' => 32, 'time_success' => (int)date('Ymd'), 'failed_status' => 82));

                $arrData += array(
                    'refund' => number_format($refund),
                    'report_refund' => array(
                        array('name' => 'Tổng đơn cần hoàn', 'value' => number_format($orderNeedRefund)),
                        array('name' => 'Thất lạc', 'value' => number_format($orderFailedRefundMissing)),
                        array('name' => 'Đối soát hoàn hàng', 'value' => number_format($orderFailedRefundReconcile)),
                    )
                );
            }

            return $this->_responseSuccess('Success', $arrData);
        }
        return $this->_responseError('Vai trò không khả dụng.');
    }
}
