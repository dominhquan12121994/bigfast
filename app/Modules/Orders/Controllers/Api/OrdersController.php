<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Controllers\Api\AbstractApiController;
use App\Helpers\StringHelper;

use App\Modules\Systems\Models\Entities\User;
use App\Modules\Orders\Models\Entities\OrderShipAssigned;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShipAssignedInterface;
use App\Modules\Operators\Models\Repositories\Contracts\PostOfficesInterface;

use App\Modules\Orders\Models\Services\OrderShipAssignedServices;
use App\Modules\Orders\Models\Services\OrderServices;
use App\Modules\Orders\Exports\OrderExport;

use App\Modules\Orders\Constants\OrderConstant;
use App\Modules\Orders\Resources\OrdersResource;
use App\Modules\Orders\Resources\OrderPickupResource;
use App\Modules\Orders\Resources\OrderShipperResource;
use App\Modules\Systems\Events\CreateLogEvents;

class OrdersController extends AbstractApiController
{

    protected $_ordersInterface;
    protected $_shopsInterface;
    protected $_orderServices;
    protected $_shopAddressInterface;
    protected $_orderShipAssignedInterface;
    protected $_orderShipAssignedServices;
    protected $_postOfficesInterface;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrdersInterface $ordersInterface,
                                ShopsInterface $shopsInterface,
                                OrderServices $orderServices,
                                ShopAddressInterface $shopAddressInterface,
                                OrderShipAssignedServices $orderShipAssignedServices,
                                OrderShipAssignedInterface $orderShipAssignedInterface,
                                PostOfficesInterface $postOfficesInterface)
    {
        parent::__construct();

        $this->_orderServices = $orderServices;
        $this->_shopsInterface = $shopsInterface;
        $this->_ordersInterface = $ordersInterface;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_orderShipAssignedInterface = $orderShipAssignedInterface;
        $this->_orderShipAssignedServices = $orderShipAssignedServices;
        $this->_postOfficesInterface = $postOfficesInterface;
    }

    public function getByLadingCode(Request $request)
    {
        if (!$request->has('lading_code')) {
            return $this->_responseError('Không tìm thấy mã vận đơn!');
        }

        $order = $this->_ordersInterface->getOne(array('lading_code' => $request->input('lading_code')), array('with' => array('servicetype', 'receiver', 'sender', 'logs', 'shop.getAddress')));

        if (!$order) {
            return $this->_responseError('Không tìm thấy mã vận đơn!');
        }

        $shopId = $order->shop_id;
        $shop = $order->shop;

        /*
         * Order log
         */
        $logsData = array();
        $logs = $order->logs;
        if ($logs) {
            foreach ($logs as $log) {
                $logsData[] = date('H:i', strtotime($log->timer)) . ', ngày ' . date('d-m-Y', strtotime($log->timer)) . ': ' . $log->note1;

//                $dt = strtotime($log->timer);
//                $getThu = strtolower(date('l', $dt));
//                $getDate = OrderConstant::weekday[$getThu] . ' ' . date('d/m/Y', $dt);
//
//                $logsData[$getDate] = isset($logsData[$getDate]) ? $logsData[$getDate] : array();
//                array_unshift($logsData[$getDate], $log);
            }
        }

        /*
         * Get product info
         */
//        $products = $this->_orderProductInterface->getMore(array('order_id' => $order->id));
//        $countProduct = (count($products) > 0) ? count($products) : 1;

        /*
         * Get shop info
         */
//        $shopAddressAll = $order->shop->getAddress;
//        $shopAddress = (count($shopAddressAll) > 0) ? $shopAddressAll[0] : null;

        $data = new \stdClass();
        $data->receiver = $order->receiver;
        if ( $order->receiver ) {
            $data->receiver->name = StringHelper::hiddenText($data->receiver->name);
            $data->receiver->phone = StringHelper::hiddenText($data->receiver->phone, 'phone');
            $data->receiver->address = '****** ' . $data->receiver->districts->name . ', ' . $data->receiver->provinces->name;
        }
        $data->sender = $order->sender;
        if ( $order->sender ) {
            $data->sender->name = StringHelper::hiddenText($data->sender->name);
            $data->sender->phone = StringHelper::hiddenText($data->sender->phone, 'phone');
            $data->sender->address = '****** ' . $data->sender->districts->name . ', ' . $data->sender->provinces->name;
        }
        $data->order_info = array('cod' => '******' . ' vnđ', 'payfee' => OrderConstant::payfees[$order->payfee]);
        $data->order_status = OrderConstant::status[$order->status]['name'];
        $data->logs = $logsData;

        return $this->_responseSuccess('Success', $data);
    }

    public function export(Request $request)
    {
        //Lưu log
        event(new CreateLogEvents([], 'orders', 'orders_export'));

        return Excel::download(new OrderExport, 'orders.xlsx');
    }

    public function getModalAction(Request $request)
    {
        $currentUser = $request->user();

        $statusList = OrderConstant::status;
        if ($currentUser->getRoleNames()[0] === 'pushsale') {
            $statusList = OrderConstant::statusPushsale;
        }
        
        if ($currentUser->getRoleNames()[0] === 'coordinator') {
            $statusList = OrderConstant::statusCoordinator;
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|numeric',
            'status_detail' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $filter['status'] = $request->input('status');
        $filter['status_detail'] = $request->input('status_detail');

        $post_offices = $this->_postOfficesInterface->getMore($filter, array('with' => ['provinces', 'districts', 'wards']));
        $userAccountancy = User::role('accountancy')->get();
        $userShipper = User::role('shipper')->get();
        $userPickup = User::role('pickup')->get();
        $userRefund = User::role('refund')->get();

        return view('Orders::orders.modal-action', [
            'filter' => $filter,
            'currentUserApi' => $currentUser,
            'apiPostOffices' => $post_offices,
            'apiUserAccountancy' => $userAccountancy,
            'apiUserShipper' => $userShipper,
            'apiUserPickup' => $userPickup,
            'apiUserRefund' => $userRefund,
            'statusList' => $statusList,
        ]);
    }

    public function getByShipper(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'in:shipper,pickup,refund',
            'status' => 'numeric',
            'page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $arrStatusDetail = array(0);
        $user = Auth::guard('admin-api')->user();
        $user_id = $user->id;

        $user_role = $user->getRoleNames()[0];
        $user_roles = $user->getRoleNames()->toArray();
        if (count($user_roles) > 1) {
            if ($request->has('type')) {
                if (in_array($request->input('type'), $user_roles)) {
                    $user_role = $request->input('type');
                }
            }
        }

        if ($user_role === 'shipper') {
            $arrStatusDetail = OrderConstant::splitShipperDetail['shipper'];
        } elseif ($user_role === 'pickup') {
            $arrStatusDetail = OrderConstant::splitShipperDetail['pickup'];
        } elseif ($user_role === 'refund') {
            $arrStatusDetail = OrderConstant::splitShipperDetail['refund'];
        }

        $statusActive = (int)$request->input('status', $arrStatusDetail[0]);
        $user_type = '';
        $totalDone = 0;

        if ($user_role === 'shipper') {
            $user_type = 'shipper';
            if ($statusActive != 51) {
                $conditions = array('assignUserIdShip' => $user_id, 'assignUserRoleShip' => $user_role, 'assignStatusShip' => $statusActive, 'statusDetailShip' => $arrStatusDetail);
                $orders = $this->_ordersInterface->customPaginate($conditions, array(
                    'with' => array('extra', 'receiver', 'receiver.provinces', 'receiver.districts', 'receiver.wards', 'shop', 'logs'),
                    'orderBy' => array('status_detail', 'updated_at'),
                    'direction' => array('ASC')
                ), 5);
            } else {
                $conditions = array('assignUserIdShipDone' => $user_id, 'assignUserRoleShipDone' => $user_role);
                $orders = $this->_ordersInterface->customPaginate($conditions, array(
                    'with' => array('extra', 'receiver', 'receiver.provinces', 'receiver.districts', 'receiver.wards', 'shop', 'logs'),
                    'orderBy' => array('status_detail', 'updated_at'),
                    'direction' => array('ASC')
                ), 5);
            }
            $conditions = array('assignUserIdShipDone' => $user_id, 'assignUserRoleShipDone' => $user_role);
            $totalDone = $this->_ordersInterface->checkExist($conditions);

        } elseif ($user_role === 'pickup') {

            $orders = array('shops' => array());
            $user_type = 'pickup';

            if ($statusActive != 22) {
                $payload = array(
                    'user_id' => $user_id,
                    'user_role' => $user_role,
                    'statusActive' => $statusActive,
                    'arrStatusDetail' => $arrStatusDetail
                );
                $ordersPickup = $this->_orderShipAssignedServices->getOrderPickup($payload);
                $orders['shops'] = array_merge($orders['shops'], $ordersPickup);
            }

            $payload = array(
                'user_id' => $user_id,
                'user_role' => $user_role,
                'statusActive' => $statusActive
            );
            $ordersPickupDone = $this->_orderShipAssignedServices->getOrderPickupDone($payload, $totalDone);
            $orders['shops'] = array_merge($orders['shops'], $ordersPickupDone);

        } elseif ($user_role === 'refund') {
            $orders = array('shops' => array());
            $user_type = 'refund';

            if ($statusActive != 82) {
                $payload = array(
                    'user_id' => $user_id,
                    'user_role' => $user_role,
                    'statusActive' => $statusActive,
                    'arrStatusDetail' => $arrStatusDetail
                );
                $ordersRefund = $this->_orderShipAssignedServices->getOrderRefund($payload);
                $orders['shops'] = array_merge($orders['shops'], $ordersRefund);
            }

            $payload = array(
                'user_id' => $user_id,
                'user_role' => $user_role,
                'statusActive' => $statusActive
            );
            $ordersRefundDone = $this->_orderShipAssignedServices->getOrderRefundDone($payload, $totalDone);
            $orders['shops'] = array_merge($orders['shops'], $ordersRefundDone);
        }

        $payload = array(
            'user_id' => $user_id,
            'user_role' => $user_role,
            'user_type' => $user_type,
            'arrStatusDetail' => $arrStatusDetail
        );
        $arrCount = $this->_orderShipAssignedServices->countByStatus($payload);

        $orders['status'] = $arrCount;
        $orders['userType'] = $user_type;
        $orders['totalDone'] = $totalDone;
        $orders['statusActive'] = $statusActive;
        $orders['arrStatusByType'] = $arrStatusDetail;

        return $this->_responseSuccess('Success', new OrderShipperResource($orders));
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:3',
            'type' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $arrStatusDetail = array(0);
        $user = Auth::guard('admin-api')->user();
        $user_id = $user->id;
        $user_role = $user->getRoleNames()[0];
        if ($user_role === 'shipper') {
            $arrStatusDetail = OrderConstant::splitShipperDetail['shipper'];
        } elseif ($user_role === 'pickup') {
            $arrStatusDetail = OrderConstant::splitShipperDetail[$request->input('type', 'pickup')];
        }

        $searchTxt = $request->input('q');
        if ($user_role === 'shipper') {

            $conditions = array(
                'assignUserIdSearch' => $user_id,
                'assignStatus' => 2,
                'searchTxt' => $searchTxt
            );
            $orders = $this->_ordersInterface->customPaginate($conditions, array(
                'with' => array('extra', 'receiver', 'receiver.provinces', 'receiver.districts', 'receiver.wards', 'shop'),
                'orderBy' => 'status_detail',
                'direction' => 'ASC',
            ), 5);

        } elseif ($user_role === 'pickup') {

            $orders = array('shops' => array());
            $user_type = $request->input('type', 'pickup');
            if ($user_type === 'pickup') {

                $payload = array(
                    'assignUserIdSearch' => $user_id
                );
                $ordersPickup = $this->_orderShipAssignedServices->getOrderPickupSearch($payload);
                $orders['shops'] = array_merge($orders['shops'], $ordersPickup);
            } else {

                $payload = array(
                    'assignUserIdSearch' => $user_id
                );
                $ordersRefund = $this->_orderShipAssignedServices->getOrderRefundSearch($payload);
                $orders['shops'] = array_merge($orders['shops'], $ordersRefund);
            }
        }

        return $this->_responseSuccess('Success', new OrderShipperResource($orders));
    }

    /**
     * Create orders
     */
    public function createByDraft(Request $request, $shop = '')
    {
        $validator = Validator::make($request->all(), [
            'shopId' => 'required|numeric',
            'userId' => 'required|numeric',
            'draftKey' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $arrError = array();
        $arrSuccess = array();
        $draftKey = explode(',', $request->input('draftKey'));
        if (count($draftKey) > 0) {
            $keyRedis = ':admin:'.$request->input('userId').':draft_order:shop_'.$request->input('shopId');
            if ($shop === 'shop')
                $keyRedis = ':shop:'.$request->input('shopId').':draft_order';
            $draftList = Redis::lrange($keyRedis, 0, -1);
            $draftList = collect($draftList)->filter()->all();

            foreach ($draftKey as $key) {
                $payload = json_decode($draftList[$key], true);
                $payload['user_id'] = (int)$request->input('userId');
                $payload['keyRedisDraft'] = $keyRedis . '_' . ($key + 1);
                $validatorOrder = Validator::make($payload, [
                    'shopId' => 'required|numeric',
                    'senderId' => 'required|numeric',
                    'receiverName' => 'required',
                    'receiverPhone' => 'required',
                    'receiverAddress' => 'required',
                    'receiverProvinces' => 'required|numeric',
                    'receiverDistricts' => 'required|numeric',
                    'receiverWards' => 'required|numeric',
                    'weight' => 'required|numeric',
                    'service_type' => 'required|exists:order_services,alias'
                ]);

                if ($validatorOrder->fails()) {
                    // create fail
                    $arrError[] = $validatorOrder->errors();
                } else {
                    $arrSuccess = $payload;
                    $dataRes = $this->_orderServices->crudStore($payload);
                    if ($dataRes->result) {
                        Redis::lset($keyRedis, $key, null);
                        setcookie($payload['keyRedisDraft'], null, -1, '/');
                    }
                }
            }
        }

        return $this->_responseSuccess('Success', array('success' => $arrSuccess, 'error' => $arrError));
    }

    public function updateStatusOrder(Request $request)
    {
        $user = Auth::guard('admin-api')->user();
        $user_role = $user->getRoleNames()[0];

        $validator = Validator::make($request->all(), [
            'orders' => 'required|array|min:1',
            'status_detail' => 'required|integer'
        ]);
        $status_detail = (int)$request->input('status_detail');

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $payload = $request->all();
        $payload['user_type'] = 'user';
        if ($user_role === 'shop') {
            $payload['user_type'] = 'shop';
        }
        $payload['user_id'] = $user->id;
        $orders = $request->input('orders');

        switch ($status_detail) {
            case 12:
                $response = $this->_orderServices->assignShipperPick($orders, $payload);
                break;
            case 13:
                $response = $this->_orderServices->pickFail($orders, $payload);
                break;
            case 21:
                $response = $this->_orderServices->pickSuccess($orders, $payload);
                break;
            case 22:
                $response = $this->_orderServices->wareHouse($orders, $payload);
                break;
            case 23:
                $response = $this->_orderServices->assignShipperSend($orders, $payload);
                break;
            case 24:
                $response = $this->_orderServices->sendFail($orders, $payload);
                break;
            case 25:
                $response = $this->_orderServices->store($orders, $payload);
                break;
            case 31:
                $response = $this->_orderServices->setRefund($orders, $payload);
                break;
            case 32:
                $response = $this->_orderServices->assignRefund($orders, $payload);
                break;
            case 33:
                $response = $this->_orderServices->refundFail($orders, $payload);
                break;
            case 34:
                $response = $this->_orderServices->confirmRefund($orders, $payload);
                break;
            case 35:
                $response = $this->_orderServices->approvalRefund($orders, $payload);
                break;
            case 36:
                $response = $this->_orderServices->wareHouseRefund($orders, $payload);
                break;
            case 41:
                $response = $this->_orderServices->confirmResend($orders, $payload);
                break;
            case 51:
                $response = $this->_orderServices->sendSuccess($orders, $payload);
                break;
            case 52:
                $response = $this->_orderServices->refundSuccess($orders, $payload);
                break;
            case 61:
                $response = $this->_orderServices->cancelOrder($orders, $payload);
                break;
            case 71:
                $response = $this->_orderServices->missing($orders, $payload);
                break;
            case 72:
                $response = $this->_orderServices->damaged($orders, $payload);
                break;
            case 73:
                $response = $this->_orderServices->missingConfirm($orders, $payload);
                break;
            case 74:
                $response = $this->_orderServices->damagedConfirm($orders, $payload);
                break;
            case 81:
                $response = $this->_orderServices->reconcileSend($orders, $payload);
                break;
            case 82:
                $response = $this->_orderServices->reconcileRefund($orders, $payload);
                break;
            case 83:
                $response = $this->_orderServices->reconcileMissing($orders, $payload);
                break;
            case 84:
                $response = $this->_orderServices->reconcileDamaged($orders, $payload);
                break;
            case 91:
                $response = $this->_orderServices->collectMoney($orders, $payload);
                break;
            default:
                return $this->_responseError($validator->errors());
        }

        if ($response->result) {
            $orders = collect($response->orders);
            return $this->_responseSuccess('Success', new OrdersResource($orders));
        }
        else
            return $this->_responseError($response->error);
    }

    public function loadMore(Request $request) {
        $validator = Validator::make($request->all(), [
            'page' => 'required',
            'status' => 'required',
            'status_detail' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Vui lòng truyền đủ dữ liệu');
        }
        $filter = $request->only('page', 'status', 'status_detail', 'limit', 'shop_id', 'created_range');
        $filter['created_range'] = explode(',', $filter['created_range']);
        $ordersList = $this->_ordersInterface->getMore(
            $filter,
            array(
                'with' => array('shop', 'extra', 'sender.provinces', 'receiver.provinces'),
                'orderBy' => array('status_detail', 'updated_at'),
                'direction' => array('ASC')
            ), $filter['limit']);
        $data = [];
        $count = 0;
        $next = true;
        if ( $ordersList->lastPage() == $filter['page']) {
            $next = false;
        }
        foreach ($ordersList as $order) {
            $count++;
            $data[] = view('Orders::orders.shipper.mobile.order-item-detail', [
                'count' => $count,
                'order' => $order,
                'constantStatus'    => OrderConstant::status,
                'constantPayfees'   => OrderConstant::payfees,
            ])->render();
        }

        return $this->_responseSuccess('Success', [ 'html' => $data, 'next' => $next ]);
    }
}
