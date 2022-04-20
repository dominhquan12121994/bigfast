<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Services;

use App\Modules\Orders\Constants\OrderConstant;
use Agent;
use Throwable;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Redis;

use App\Helpers\OrderHelper;

use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderLogInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderTraceInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderExtraInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderQueueInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderReceiverInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderProductInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingCodInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingFeeInsuranceInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShipAssignedInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderStatusOvertimeInterface;

use App\Modules\Orders\Models\Services\CalculatorFeeServices;
use App\Modules\Orders\Models\Entities\Orders;

use App\Modules\Operators\Models\Repositories\Contracts\PostOfficesInterface;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;
use App\Modules\Operators\Models\Repositories\Contracts\WardsInterface;

use App\Modules\Systems\Models\Repositories\Contracts\UsersInterface;
use App\Modules\Systems\Models\Entities\User;
use App\Modules\Systems\Events\CreateLogEvents;
use App\Modules\Systems\Services\NotificationServices;


class OrderServices
{
    protected $_calculatorFee;
    protected $_ordersInterface;
    protected $_orderFeeInterface;
    protected $_orderLogInterface;
    protected $_orderTraceInterface;
    protected $_orderExtraInterface;
    protected $_orderQueueInterface;
    protected $_orderReceiverInterface;
    protected $_orderProductInterface;
    protected $_orderSettingCodInterface;
    protected $_orderSettingFeeInsuranceInterface;
    protected $_orderShipAssignedInterface;
    protected $_orderStatusOvertimeInterface;

    protected $_usersInterface;
    protected $_shopsInterface;
    protected $_shopAddressInterface;

    protected $_postOfficesInterface;
    protected $_provinceInterface;
    protected $_districtInterface;
    protected $_wardInterface;
    protected $_notificationServices;

    public function __construct(OrdersInterface $ordersInterface,
                                CalculatorFeeServices $calculatorFee,
                                OrderFeeInterface $orderFeeInterface,
                                OrderLogInterface $orderLogInterface,
                                OrderTraceInterface $orderTraceInterface,
                                OrderExtraInterface $orderExtraInterface,
                                OrderQueueInterface $orderQueueInterface,
                                OrderReceiverInterface $orderReceiverInterface,
                                OrderProductInterface $orderProductInterface,
                                OrderSettingCodInterface $orderSettingCodInterface,
                                OrderSettingFeeInsuranceInterface $orderSettingFeeInsuranceInterface,
                                OrderShipAssignedInterface $orderShipAssignedInterface,
                                OrderStatusOvertimeInterface $orderStatusOvertimeInterface,
                                UsersInterface $usersInterface,
                                ShopsInterface $shopsInterface,
                                ShopAddressInterface $shopAddressInterface,
                                PostOfficesInterface $postOfficesInterface,
                                ProvincesInterface $provincesInterface,
                                DistrictsInterface $districtsInterface,
                                NotificationServices $notificationServices,
                                WardsInterface $wardsInterface)
    {

        $this->_calculatorFee = $calculatorFee;
        $this->_usersInterface = $usersInterface;
        $this->_shopsInterface = $shopsInterface;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_orderSettingCodInterface = $orderSettingCodInterface;
        $this->_orderSettingFeeInsuranceInterface = $orderSettingFeeInsuranceInterface;
        $this->_orderShipAssignedInterface = $orderShipAssignedInterface;
        $this->_ordersInterface = $ordersInterface;
        $this->_orderFeeInterface = $orderFeeInterface;
        $this->_orderLogInterface = $orderLogInterface;
        $this->_orderTraceInterface = $orderTraceInterface;
        $this->_orderExtraInterface = $orderExtraInterface;
        $this->_orderQueueInterface = $orderQueueInterface;
        $this->_orderReceiverInterface = $orderReceiverInterface;
        $this->_orderProductInterface = $orderProductInterface;
        $this->_orderStatusOvertimeInterface = $orderStatusOvertimeInterface;
        $this->_postOfficesInterface = $postOfficesInterface;
        $this->_provincesInterface = $provincesInterface;
        $this->_districtsInterface = $districtsInterface;
        $this->_wardsInterface = $wardsInterface;
        $this->_notificationServices = $notificationServices;
    }

    public function countByStatus($shopId = 0, $filter = array())
    {
        if ($shopId) {
            $filter['shop_id'] = $shopId;
        }

        $beginDate = date('Ym01');
        $endDate = date('Ymd');

        $filter['status'] = 0;
        $filter['created_range'] = array($beginDate, $endDate);

        /* Get list status */
        $currentUser = null;
        if (request()->is('admin/*')) {
            if (Auth::guard('admin')->check()) {
                $currentUser = \Auth::guard('admin')->user();
            }
        } else {
            if (Auth::guard('shop')->check()) {
                $currentUser = \Auth::guard('shop')->user();
            }
            if (Auth::guard('shopStaff')->check()) {
                $currentUser = \Auth::guard('shopStaff')->user();
            }
        }

        if (!$currentUser) abort(401);

        $statusMain = OrderConstant::status;

        if ($currentUser->getRoleNames()[0] === 'pushsale') {
            $statusMain = OrderConstant::statusPushsale;
        }

        if ($currentUser->getRoleNames()[0] === 'coordinator') {
            $statusMain = OrderConstant::statusCoordinator;
        }

        $checkBlockStatus = false;
        if (isset($filter['status_detail_shipper'])) {
            $roles = $currentUser->getRoleNames()->toArray();
            $statusMain = array();
            if (in_array('shipper', $roles)) {
                $statusMain = $statusMain + OrderConstant::statusShipperShip;
            }
            if (in_array('pickup', $roles)) {
                $statusMain = $statusMain + OrderConstant::statusShipperPickup;
            }
            if (in_array('refund', $roles)) {
                $statusMain = $statusMain + OrderConstant::statusShipperRefund;
            }
        } else {
            if ($currentUser) {
                $roles = $currentUser->getRoleNames();
                if ($roles[0] === 'shop') {
                    $checkBlockStatus = true;
                }
            }
            $statusMain[0]['count'] = $this->_orderQueueInterface->checkExist($filter);
            if ($checkBlockStatus) {
                unset($statusMain[9]);
            }
        }

        if (isset($filter['assignUserId'])) {
            $buildQuery = Orders::groupBy('orders.status')
                ->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id')
                ->select('orders.status', DB::raw('count(distinct(`orders`.`id`)) as total'))
                ->whereIn('order_ship_assigned.user_role', $filter['assignUserRole'])
                ->where('order_ship_assigned.user_id', $filter['assignUserId'])
//                ->whereIn('orders.status_detail', $filter['status_detail_shipper'])
//                ->whereIn('order_ship_assigned.failed_status', $filter['status_detail_shipper'])
                ->whereBetween('orders.created_date', array((int)$beginDate, (int)$endDate));

            if (count($filter['status_detail_shipper']) > 0) {
                $buildQuery->where(function($bq) use ($filter) {
                    foreach ($filter['status_detail_shipper'] as $status_detail) {
                        $bq->orWhere(function($q) use ($status_detail) {
                            $q->where('orders.status_detail', $status_detail);
                            $q->where('order_ship_assigned.failed_status', $status_detail);
                        });
                    }
                });
            }
        } else {
            $buildQuery = Orders::groupBy('status')
                ->select('status', DB::raw('count(*) as total'))
                ->whereBetween('created_date', array((int)$beginDate, (int)$endDate));
            if ($shopId) {
                $buildQuery->where('shop_id', $shopId);
            }
        }
        $countOrderByStatus = $buildQuery->get();
        foreach ($statusMain as $status => $item) {
            if ($status > 0) {
                $countStatus = $countOrderByStatus->filter(function ($value, $key) use ($status) {
                    return $value->status === $status;
                })->sum('total');

                if ($status === 5 && $checkBlockStatus) {
                    $countStatus += $countOrderByStatus->filter(function ($value, $key) {
                        return $value->status === 9;
                    })->sum('total');
                }

//                $arrStatusCountCache = array(6, 8);
//                if (in_array($status, $arrStatusCountCache)) {
                    // get count befor from cache
                    $endDateCache = date('Ymd', strtotime('-1 day', strtotime($beginDate)));
                    $keyRedis = ':orders:count_status:'.$status.':'.$shopId.':befor_' . $endDateCache;
                    if (isset($filter['assignUserId'])) {

                        if ($status === 5) {
                            $conditions = array('assignUserIdShipDone' => $filter['assignUserId'], 'assignUserRoleShipDone' => $filter['assignUserRole']);
                            $countStatus = $this->_ordersInterface->checkExist($conditions);
                        }
                        if ($status === 2 && in_array('pickup', $filter['assignUserRole'])) {
                            $conditions = array(
                                'assignUserRoleDone' => 'pickup',
                                'assignUserIdDone' => $filter['assignUserId'],
                                'orderDoneStatus'  => 1
                            );
                            $countStatus = $this->_ordersInterface->checkExist($conditions);
                        }
                        if ($status === 8 && in_array('refund', $filter['assignUserRole'])) {
                            $conditions = array(
                                'assignUserRoleDone' => 'refund',
                                'assignUserIdDone' => $filter['assignUserId'],
                                'orderDoneStatus'  => 3
                            );
                            $countStatus = $this->_ordersInterface->checkExist($conditions);
                        }

                        $keyRedis = ':orders:count_status:'.$status.':'.$shopId.':'.implode('-', $filter['assignUserRole']).':'.$filter['assignUserId'].':befor_' . $endDateCache;
                        $countStatusCache = Redis::get($keyRedis);
                        if (is_null($countStatusCache) || 1===1) {
                            $filter['status'] = $status;
                            $filter['created_range'] = array("20210301", $endDateCache);
                            $countStatusQuery = Orders::leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id')
                                ->select(DB::raw('count(distinct(`orders`.`id`)) as total'))
                                ->whereIn('order_ship_assigned.user_role', $filter['assignUserRole'])
                                ->where('order_ship_assigned.user_id', $filter['assignUserId']);
                            if (in_array($status, array(1, 2, 3))) {
                                $countStatusQuery->where('order_ship_assigned.status', $filter['status']);
                            }
                            $countStatusQuery->where('orders.status', $filter['status'])
                                ->whereBetween('orders.created_date', $filter['created_range']);
                            $countStatusQuery = $countStatusQuery->get();
                            $countStatusCache = $countStatusQuery[0]->total;
                            Redis::set($keyRedis, $countStatusCache);
                        }
                        $countStatus += $countStatusCache;
                    } else {
                        $countStatusCache = Redis::get($keyRedis);
                        if (is_null($countStatusCache) || 1===1) {
                            $filter['status'] = $status;
                            $filter['created_range'] = array("20210301", $endDateCache);
                            $countStatusCache = $this->_ordersInterface->checkExist($filter);
                            Redis::set($keyRedis, $countStatusCache);
                        }
                        $countStatus += $countStatusCache;
                    }
//                }

                $statusMain[$status]['count'] = $countStatus;
            }
        }

        return $statusMain;
    }

    public function getOne($orderId)
    {
        $order = $this->_ordersInterface->getById($orderId);

        if (!$order) {
            abort(404);
        }

        $shopId = $order->shop_id;
        $shop = $this->_shopsInterface->getById($shopId);
        if (!$shop) {
            abort(404);
        }

        /*
         * Get product info
         */
        $products = $this->_orderProductInterface->getMore(array('order_id' => $order->id));

        /*
         * Get receiver info
         */
        $receiver = $this->_orderReceiverInterface->getById($order->receiver_id);
        $receiver->p_name = $receiver->provinces->name;
        $receiver->d_name = $receiver->districts->name;
        $receiver->w_name = $receiver->wards->name;
        if (!$receiver) {
            abort(404);
        }

        $order_extra = $this->_orderExtraInterface->getById($order->id);
        $shop_address = $this->_shopAddressInterface->getById($order->sender_id, array(), array('withTrashed' => true));
        $shipper = $this->_orderShipAssignedInterface->getOne(array(
            'order_id' => $order_extra->id,
            'user_role' => 'shipper'
        ), array(
            'with' => 'user'
        ));
        $dataRes = new \stdClass();
        $dataRes->status = true;
        $dataRes->data = new \stdClass();
        $dataRes->data->info = $order->makeHidden([
            'created_date', 'collect_money_date',
            'shop_id', 'sender_id', 'refund_id', 'receiver_id',
            'deleted_at'
        ])->toArray();
        $dataRes->data->extra = $order_extra->makeHidden(['id'])->toArray();
        $dataRes->data->shop = $shop->makeHidden(['email_verified_at', 'menuroles', 'created_at', 'updated_at', 'deleted_at'])->toArray();
        $dataRes->data->sender = $shop_address->makeHidden(['id', 'shop_id', 'created_at', 'updated_at', 'deleted_at'])->toArray();
        $dataRes->data->receiver = $receiver->makeVisible(['p_name', 'd_name', 'w_name'])->makeHidden(['id', 'created_at', 'updated_at', 'deleted_at', 'provinces', 'districts', 'wards'])->toArray();
        $dataRes->data->products = $products->makeHidden(['id', 'order_id', 'created_at', 'updated_at', 'deleted_at'])->toArray();
        if ($shipper) {
            $dataRes->data->shipper = array(
                'name' => $shipper->user->name,
                'phone' => $shipper->user->phone,
            );
        } else {
            $dataRes->data->shipper = null;
        }
        return json_encode($dataRes);
    }

    public function crudStore($payload)
    {
        try {
            $log_data = [];

            DB::beginTransaction();

            // check rules
            $payload['quantity_products'] = (int)$payload['quantity_products'];
            if ($payload['quantity_products'] < 1) {
                throw new \Exception('Chưa nhập thông tin sản phẩm');
            }
            if ($payload['quantity_products'] !== count($payload['addProductName'])) {
                throw new \Exception('Chưa nhập thông tin sản phẩm');
            }

            $address_refund = $payload['address_refund'];
            if ($address_refund === 0 || $address_refund === "0") {
                $payload['refundId'] = $payload['senderId'];
            } elseif ($address_refund === 'add') {
                // them moi dia chi chuyen hoan
                if (substr($payload['refundPhone'], 0, 1) !== '0') $payload['refundPhone'] = '0' . $payload['refundPhone'];
                $addAddress = $this->_shopAddressInterface->create(array(
                    'shop_id'   => $payload['shopId'],
                    'p_id'      => $payload['refundProvinces'],
                    'd_id'      => $payload['refundDistricts'],
                    'w_id'      => $payload['refundWards'],
                    'type'      => 'refund',
                    'name'      => $payload['refundName'],
                    'phone'     => $payload['refundPhone'],
                    'address'   => $payload['refundAddress']
                ));
                $payload['refundId'] = $addAddress->id;
            } else {
                // lay tu danh sach
                $payload['refundId'] = (int)$address_refund;
            }
            if ($payload['refundId'] === 0) {
                throw new \Exception('Không tìm thấy địa chỉ hoàn hàng');
            }

            // them nguoi nhan - kiem tra ton tai sdt
            if (substr($payload['receiverPhone'], 0, 1) !== '0') $payload['receiverPhone'] = '0' . $payload['receiverPhone'];
            $receiver = $this->_orderReceiverInterface->create(array(
                'name'      => $payload['receiverName'],
                'phone'     => $payload['receiverPhone'],
                'address'   => $payload['receiverAddress'],
                'p_id'      => $payload['receiverProvinces'],
                'd_id'      => $payload['receiverDistricts'],
                'w_id'      => $payload['receiverWards']
            ));
//            $receiver = $this->_orderReceiverInterface->getOne(array('phone' => $payload['receiverPhone']));
//            if (!$receiver) {
//                $receiver = $this->_orderReceiverInterface->create(array(
//                    'name'      => $payload['receiverName'],
//                    'phone'     => $payload['receiverPhone'],
//                    'address'   => $payload['receiverAddress'],
//                    'p_id'      => $payload['receiverProvinces'],
//                    'd_id'      => $payload['receiverDistricts'],
//                    'w_id'      => $payload['receiverWards']
//                ));
//            }

            // tinh phi
            $sendAddress = $this->_shopAddressInterface->getById($payload['senderId']);
            $payloadFee = array(
                'p_id_send' => $sendAddress->p_id,
                'p_id_receive' => $payload['receiverProvinces'],
                'd_id_receive' => $payload['receiverDistricts'],
                'service' => $payload['service_type'],
                'weight' => $payload['weight']
            );
            $check_transport_fee = $this->_calculatorFee->calculatorFee($payloadFee);
            if (!$check_transport_fee->status) {
                throw new \Exception('Dữ liệu không chính xác');
            }
            $expertReceiver = date('d-m-Y H:i:s', strtotime('+ ' . $check_transport_fee->timePick->to . ' day', strtotime($payload['expect_pick'])));

            // phi van chuyen
            $transport_fee = $check_transport_fee->result;

            // phi thu cod
            $cod_fee = 0;
            if (substr($payload['service_type'], 0, 2) === 'dg' && substr($payload['service_type'], -1) === 'k') {
                $cod_fee = 0;
            } else {
                $check_cod_fee = $this->_orderSettingCodInterface->getOne(array('cod' => $payload['cod']));
                if ($check_cod_fee) {
                    // percent | money
                    if ($check_cod_fee->type === 'money')
                        $cod_fee = $check_cod_fee->value;
                    else
                        $cod_fee = ($check_cod_fee->value / 100) * $payload['cod'];
                }
            }

            // phi bao hiem
            $insurance_fee = 0;
            if (substr($payload['service_type'], 0, 2) === 'dg' && substr($payload['service_type'], -1) === 'k') {
                $insurance_fee = 0;
            } else {
                $check_insurance_fee = $this->_orderSettingFeeInsuranceInterface->getOne(array('insurance' => $payload['insurance_value']));
                if ($check_insurance_fee) {
                    $insurance_fee = $check_insurance_fee->value;
                }
            }

            $total_fee = $transport_fee + $cod_fee + $insurance_fee;

            // them don hang
            $created_date = (int) Carbon::now()->format('Ymd');
            $lading_code = $payload['lading_code'] ?? $this->generateLandingCode(); // gen unique
            $order = $this->_ordersInterface->create(array(
                'shop_id'           => $payload['shopId'],
                'sender_id'         => $payload['senderId'],
                'refund_id'         => $payload['refundId'],
                'receiver_id'       => $receiver->id,
                'lading_code'       => $lading_code,
                'status'            => config('order.status.default'),
                'status_detail'     => config('order.status.default_detail'),
                'transport_fee'     => $transport_fee,
                'total_fee'         => $total_fee,
                'cod'               => $payload['cod'],
                'insurance_value'   => $payload['insurance_value'],
                'service_type'      => $payload['service_type'],
                'payfee'            => $payload['payfee'],
                'weight'            => $payload['weight'],
                'height'            => $payload['height'],
                'width'             => $payload['width'],
                'length'            => $payload['length'],
                'created_date'      => $created_date
            ));

            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $order,
            ];

            // them thong tin bo sung
            $order_extra = $this->_orderExtraInterface->create(array(
                'id'                => $order->id,
                'client_code'       => $payload['client_code'],
                'note1'             => $payload['note1'],
                'note2'             => $payload['note2'],
                'receiver_name'     => $payload['receiverName'],
                'receiver_phone'    => $payload['receiverPhone'],
                'receiver_address'  => $payload['receiverAddress'],
                'receiver_p_id'     => $payload['receiverProvinces'],
                'receiver_d_id'     => $payload['receiverDistricts'],
                'receiver_w_id'     => $payload['receiverWards'],
                'expect_pick'       => $payload['expect_pick'],
                'expect_receiver'   => $expertReceiver,
            ));

            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $order_extra,
            ];

            // them sp
            $arrProductInsert = array();
            foreach ($payload['addProductName'] as $key => $productName) {
                $dataProduct = array(
                    'order_id'  => $order->id,
                    'name'      => $productName,
                    'code'      => $payload['addProductCode'][$key],
                    'price'     => $payload['addProductPrice'][$key],
                    'quantity'  => $payload['addProductSlg'][$key],
                    'created_at'=> now(),
                    'updated_at'=> now()
                );

                $validatorProd = Validator::make($dataProduct, [
                    'order_id' => 'required|numeric',
                    'name' => 'required',
                    'price' => 'required|numeric|min:0',
                    'quantity' => 'required|numeric|min:1'
                ]);

                if ($validatorProd->fails()) {
                    throw new \Exception($validatorProd->errors());
                } else {
                    $arrProductInsert[] = $dataProduct;
                }
            }
            if (count($arrProductInsert) > 0)
                $this->_orderProductInterface->insert($arrProductInsert);

            // them logs don
            $logOrder = $this->_orderLogInterface->create(array(
                'order_id'          => $order->id,
                'user_type'         => $payload['user_type'] ?? 'user',
                'user_id'           => $payload['user_id'],
                'log_type'          => 'create_order',
                'status'            => 1,
                'status_detail'     => 11,
                'note1'             => $order->receiver->address,
                'note2'             => "",
                'logs'              => json_encode($payload),
                'timer'             => now()
            ));

            //Thêm dữ liệu log
            $log_data[] = [
                'model' => $logOrder,
            ];
            //Lưu log
            event(new CreateLogEvents( $log_data, 'orders', 'orders_create' ));

            DB::commit();

            if (isset($payload['keyRedisDraft'])) {
                if ($payload['keyRedisDraft']) {
                    $lastPosition = strrpos($payload['keyRedisDraft'], '_');
                    $keyRedis = substr($payload['keyRedisDraft'], 0, $lastPosition + 1);
                    $keyLast = substr($payload['keyRedisDraft'], -1, strlen($payload['keyRedisDraft']) - strlen($keyRedis));
                    setcookie($payload['keyRedisDraft'], null, -1, '/');
                    Redis::lset($keyRedis, $keyLast, null);
                }
            }

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->order = $order;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
            $dataRes->message = $message;
        }
        return $dataRes;
    }

    public function crudUpdate($payload = array())
    {
        try {
            $log_data = [];

            DB::beginTransaction();

            // check rules
            $payload['quantity_products'] = (int)$payload['quantity_products'];
            if ($payload['quantity_products'] < 1) {
                throw new \Exception('Chưa nhập thông tin sản phẩm');
            }
            if ($payload['quantity_products'] !== count($payload['addProductName'])) {
                throw new \Exception('Chưa nhập thông tin sản phẩm');
            }

            $orderInfo = $this->_ordersInterface->getById($payload['orderId']);
            if (!$orderInfo) {
                throw new \Exception('Không thấy thông tin vận đơn');
            }

            $address_refund = $payload['address_refund'];
            if ($address_refund === 0 || $address_refund === "0") {
                $payload['refundId'] = $payload['senderId'];
            } elseif ($address_refund === 'add') {
                // them moi dia chi chuyen hoan
                if (substr($payload['refundPhone'], 0, 1) !== '0') $payload['refundPhone'] = '0' . $payload['refundPhone'];
                $addAddress = $this->_shopAddressInterface->create(array(
                    'shop_id'   => $payload['shopId'],
                    'p_id'      => $payload['refundProvinces'],
                    'd_id'      => $payload['refundDistricts'],
                    'w_id'      => $payload['refundWards'],
                    'type'      => 'refund',
                    'name'      => $payload['refundName'],
                    'phone'     => $payload['refundPhone'],
                    'address'   => $payload['refundAddress']
                ));

                //Thêm dữ liệu log
                $log_data[] = [
                    'model' => $addAddress
                ];

                $payload['refundId'] = $addAddress->id;
            } else {
                // lay tu danh sach
                $payload['refundId'] = (int)$address_refund;
            }
            if ($payload['refundId'] === 0) {
                throw new \Exception('Không tìm thấy địa chỉ hoàn hàng');
            }

            // them nguoi nhan - kiem tra ton tai sdt
            $filldata = array();
            $receiver = $orderInfo->receiver;
            if (substr($payload['receiverPhone'], 0, 1) !== '0') $payload['receiverPhone'] = '0' . $payload['receiverPhone'];
            if ($payload['receiverName'] !== $receiver->name) $filldata['name'] = $payload['receiverName'];
            if ($payload['receiverPhone'] !== $receiver->name) $filldata['phone'] = $payload['receiverPhone'];
            if ($payload['receiverAddress'] !== $receiver->address) $filldata['address'] = $payload['receiverAddress'];
            if ($payload['receiverProvinces'] !== $receiver->p_id) $filldata['p_id'] = $payload['receiverProvinces'];
            if ($payload['receiverDistricts'] !== $receiver->d_id) $filldata['d_id'] = $payload['receiverDistricts'];
            if ($payload['receiverWards'] !== $receiver->w_id) $filldata['w_id'] = $payload['receiverWards'];

            if (!empty($filldata)) {
                $old_data = $receiver;
                $orderReceiver = $this->_orderReceiverInterface->updateById($receiver->id, $filldata);
                //Thêm dữ liệu log
                $log_data[] = [
                    'old_data' => $old_data,
                ];
            }
//            $receiver = $this->_orderReceiverInterface->getOne(array('phone' => $payload['receiverPhone']));
//            if (!$receiver) {
//                $receiver = $this->_orderReceiverInterface->create(array(
//                    'name'      => $payload['receiverName'],
//                    'phone'     => $payload['receiverPhone'],
//                    'address'   => $payload['receiverAddress'],
//                    'p_id'      => $payload['receiverProvinces'],
//                    'd_id'      => $payload['receiverDistricts'],
//                    'w_id'      => $payload['receiverWards']
//                ));
//
//                //Thêm dữ liệu log
//                $log_data[] = [
//                    'model' => $receiver
//                ];
//
//            } else {
//                $filldata = array();
//                if ($payload['receiverName'] !== $receiver->name) $filldata['name'] = $payload['receiverName'];
//                if ($payload['receiverAddress'] !== $receiver->address) $filldata['address'] = $payload['receiverAddress'];
//                if ($payload['receiverProvinces'] !== $receiver->p_id) $filldata['p_id'] = $payload['receiverProvinces'];
//                if ($payload['receiverDistricts'] !== $receiver->d_id) $filldata['d_id'] = $payload['receiverDistricts'];
//                if ($payload['receiverWards'] !== $receiver->w_id) $filldata['w_id'] = $payload['receiverWards'];
//
//                if (!empty($filldata)) {
//                    $old_data = $this->_orderReceiverInterface->getById($receiver->id);
//                    $orderReceiver = $this->_orderReceiverInterface->updateById($receiver->id, $filldata);
//                    //Thêm dữ liệu log
//                    $log_data[] = [
//                        'old_data' => $old_data,
//                    ];
//                }
//            }

            // tinh phi van chuyen
            $sendAddress = $this->_shopAddressInterface->getById($payload['senderId']);
            $payloadFee = array(
                'p_id_send' => $sendAddress->p_id,
                'p_id_receive' => $payload['receiverProvinces'],
                'd_id_receive' => $payload['receiverDistricts'],
                'service' => $payload['service_type'],
                'weight' => $payload['weight']
            );
            $check_transport_fee = $this->_calculatorFee->calculatorFee($payloadFee);
            if (!$check_transport_fee->status) {
                throw new \Exception('Dữ liệu không chính xác');
            }
            $expertReceiver = date('d-m-Y H:i:s', strtotime('+ ' . $check_transport_fee->timePick->to . ' day', strtotime($payload['expect_pick'])));

            // phi van chuyen
            $transport_fee = $check_transport_fee->result;

            // phi thu cod
            $cod_fee = 0;
            $check_cod_fee = $this->_orderSettingCodInterface->getOne(array('cod' => $payload['cod']));
            if ($check_cod_fee) {
                // percent | money
                if ($check_cod_fee->type === 'money')
                    $cod_fee = $check_cod_fee->value;
                else
                    $cod_fee = ($check_cod_fee->value / 100) * $payload['cod'];
            }

            // phi bao hiem
            $insurance_fee = 0;
            $check_insurance_fee = $this->_orderSettingFeeInsuranceInterface->getOne(array('insurance' => $payload['insurance_value']));
            if ($check_insurance_fee) {
                $insurance_fee = $check_insurance_fee->value;
            }

            $total_fee = $transport_fee + $cod_fee + $insurance_fee;

            // cap nhat don hang
            $filldata = array(
                'sender_id'         => $payload['senderId'],
                'refund_id'         => $payload['refundId'],
                'receiver_id'       => $receiver->id,
                'cod'               => $payload['cod'],
                'transport_fee'     => $transport_fee,
                'total_fee'         => $total_fee,
                'insurance_value'   => $payload['insurance_value'],
                'service_type'      => $payload['service_type'],
                'payfee'            => $payload['payfee'],
                'weight'            => $payload['weight'],
                'height'            => $payload['height'],
                'width'             => $payload['width'],
                'length'            => $payload['length']
            );
            $old_data = $orderInfo;
            $update_order = $this->_ordersInterface->updateById($payload['orderId'], $filldata);
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $old_data,
            ];

            // them thong tin bo sung
            $filldata = array(
                'client_code'       => $payload['client_code'],
                'note1'             => $payload['note1'],
                'note2'             => $payload['note2'],
                'receiver_name'     => $payload['receiverName'],
                'receiver_phone'    => $payload['receiverPhone'],
                'receiver_address'  => $payload['receiverAddress'],
                'receiver_p_id'     => $payload['receiverProvinces'],
                'receiver_d_id'     => $payload['receiverDistricts'],
                'receiver_w_id'     => $payload['receiverWards'],
                'expect_pick'       => $payload['expect_pick'],
                'expect_receiver'   => $expertReceiver,
            );

            $old_data = $orderInfo->extra;
            $update_orderExtra = $this->_orderExtraInterface->updateById($payload['orderId'], $filldata);
            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $old_data,
            ];

            // add, update product info
            $arrProductInsert = array();
            foreach ($payload['addProductName'] as $key => $productName) {
                $productId = $payload['addProductId'][$key];
                if (!$productId) {
                    $dataProduct = array(
                        'order_id'  => $payload['orderId'],
                        'name'      => $productName,
                        'code'      => $payload['addProductCode'][$key],
                        'price'     => $payload['addProductPrice'][$key],
                        'quantity'  => $payload['addProductSlg'][$key],
                        'created_at'=> now(),
                        'updated_at'=> now()
                    );

                    $validatorProd = Validator::make($dataProduct, [
                        'order_id' => 'required|numeric',
                        'name' => 'required',
                        'price' => 'required|numeric|min:0',
                        'quantity' => 'required|numeric|min:1'
                    ]);

                    if ($validatorProd->fails()) {
                        throw new \Exception($validatorProd->errors());
                    } else {
                        $arrProductInsert[] = $dataProduct;
                    }
                } else {
                    // update product info
                    $filldata = array(
                        'name'      => $productName,
                        'code'      => $payload['addProductCode'][$key],
                        'price'     => $payload['addProductPrice'][$key],
                        'quantity'  => $payload['addProductSlg'][$key],
                        'updated_at'=> now()
                    );
                    $old_data = $this->_orderProductInterface->getById($productId);
                    $update_orderProduct = $this->_orderProductInterface->updateById($productId, $filldata);
                    //Thêm dữ liệu log
                    $log_data[] = [
                        'old_data' => $old_data,
                    ];
                }
            }
            if (count($arrProductInsert) > 0) {
                $insert_orderProduct = $this->_orderProductInterface->insert($arrProductInsert);
                //Thêm dữ liệu log
                $log_data[] = [
                    'model' =>  $this->_orderProductInterface,
                ];
            }

            // them logs don
            $logOrder = $this->_orderLogInterface->create(array(
                'order_id'  => $payload['orderId'],
                'user_type' => $payload['user_type'] ?? 'user',
                'user_id'   => $payload['user_id'],
                'log_type'  => 'update_order',
                'status'            => 0,
                'status_detail'     => 0,
                'note1'     => "",
                'note2'     => "",
                'logs'      => json_encode($payload),
                'timer'     => now()
            ));

            //Thêm dữ liệu log
            $log_data[] = [
                'model' =>  $logOrder,
            ];
            //Lưu log
            event(new CreateLogEvents( $log_data, 'orders', 'orders_update', $payload['orderId'] ));

//            // them hanh trinh don
//            $this->_orderTraceInterface->create(array(
//                'order_id'  => $order->id,
//                'status'    => config('order.status.default'),
//                'log_id'    => $logOrder->id,
//                'timer'     => now(),
//                'note'      => 'Lên đơn hàng thành công'
//            ));

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function assignRefund($orders = array(), $payload = array())
    {
        try {
            //
            $arrOrder = array();
            $userAssign = $this->_usersInterface->getById($payload['select_shipper']);

            if (count($orders) === 0) {
                throw new \Exception('Không có đơn hàng');
            }

            if (!$userAssign) {
                throw new \Exception('Không tìm thấy user');
            }

            if (!$userAssign->hasRole('refund')) {
                throw new \Exception('Vai trò không khả dụng');
            }

            DB::beginTransaction();

            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 3;
                    $order->status_detail = 32;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $orderShipAssigned = $this->_orderShipAssignedInterface->create([
                        'user_role'     => 'refund',
                        'user_id'       => $payload['select_shipper'],
                        'shop_id'       => $order->shop_id,
                        'order_id'      => $order_id,
                        'sender_id'     => $order->sender_id,
                        'p_id'          => $order->sender->p_id,
                        'd_id'          => $order->sender->d_id,
                        'w_id'          => $order->sender->w_id,
                        'status'     => 3,
                        'status_detail' => 32,
                        'failed_status' => 32,
                        'time_assigned' => (int)date('Ymd')
                    ]);

                    //Thêm dữ liệu log
                    $log_data[] = [
                        'model' => $orderShipAssigned,
                    ];


                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'assign_refund',
                        'status'    => 3,
                        'status_detail' => 32,
                        'note1'     => 'Đang hoàn hàng',
                        'note2'     => 'Gán shipper hoàn hàng: ' . $userAssign->name,
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );
                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents( $log_data, 'orders', 'orders_update', $order_id ));
                } else {
                    throw new \Exception('Dữ liệu không chính xác');
                }
            }

            $countOrders = count($orders);
            $payloadNoti = array(
                'sender_id' => $payload['user_id'],
                'user_id' => $userAssign->id,
                'content_data' => array(
                    7, $countOrders
                )
            );
            $this->_notificationServices->sendToUser($payloadNoti);

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function refundFail($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 3;
                    $order->status_detail = 33;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id,
                        'status'        => 3,
                        'status_detail' => 32
                    ));
                    if ($orderShipAssigned) {
                        $orderShipAssigned->time_success = 0;
                        $orderShipAssigned->time_failed = (int)date('Ymd');
                        $orderShipAssigned->failed_status = 33;
                        $orderShipAssigned->save();
                    }

                    //Thêm dữ liệu log
                    $log_data[] = [
                        'model' => $orderShipAssigned,
                    ];

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'refund_fail',
                        'status'    => 3,
                        'status_detail' => 33,
                        'note1'     => 'Hoàn hàng thất bại',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents( $log_data, 'orders', 'orders_update', $order_id ));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function assignShipperPick($orders = array(), $payload = array())
    {
        try {
            //
            $arrOrder = array();
            if ($payload['select_shipper'] !== -1) {
                $userAssign = $this->_usersInterface->getById($payload['select_shipper']);

                if (!$userAssign) {
                    throw new \Exception('Không tìm thấy user');
                }

                if (!$userAssign->hasRole('pickup')) {
                    throw new \Exception('Vai trò không khả dụng');
                }
            } else {
                $userAssign = true;
            }

            if (count($orders) === 0) {
                throw new \Exception('Không có đơn hàng');
            }

            DB::beginTransaction();

            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order && $userAssign) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 1;
                    $order->status_detail = 12;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    if ($userAssign !== true) {
                        $orderShipAssigned = $this->_orderShipAssignedInterface->create([
                            'user_role'     => 'pickup',
                            'user_id'       => $payload['select_shipper'],
                            'shop_id'       => $order->shop_id,
                            'order_id'      => $order_id,
                            'sender_id'     => $order->sender_id,
                            'p_id'          => $order->sender->p_id,
                            'd_id'          => $order->sender->d_id,
                            'w_id'          => $order->sender->w_id,
                            'status'        => 1,
                            'status_detail' => 12,
                            'failed_status' => 12,
                            'time_assigned' => (int)date('Ymd')
                        ]);
                        //Thêm dữ liệu log
                        $log_data[] = [
                            'model' => $orderShipAssigned,
                        ];
                    } else {
                        $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                            'shop_id' => $order->shop_id,
                            'order_id' => $order_id,
                            'status'        => 1,
                            'status_detail' => 12
                        ));
                        if ($orderShipAssigned) {
                            $orderShipAssigned->time_success = 0;
                            $orderShipAssigned->time_failed = 0;
                            $orderShipAssigned->failed_status = 12;
                            $orderShipAssigned->save();
                        }
                    }

                    $note2 = ($userAssign === true) ? 'Nhân viên kho thu gom lại' : 'Gán nhân viên kho: ' . $userAssign->name;

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'assign_shipper_pick',
                        'status'    => 1,
                        'status_detail' => 12,
                        'note1'     => 'Đang lấy hàng',
                        'note2'     => $note2,
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents( $log_data, 'orders', 'orders_update', $order_id));
                } else {
                    throw new \Exception('Dữ liệu không chính xác');
                }
            }

            $shipperId = (int)$payload['select_shipper'];
            if ($shipperId !== -1) {
                $countOrders = count($orders);
                $payloadNoti = array(
                    'sender_id' => $payload['user_id'],
                    'user_id' => $shipperId,
                    'content_data' => array(
                        1, $countOrders
                    ),
                );
                $this->_notificationServices->sendToUser($payloadNoti);
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function assignShipperSend($orders = array(), $payload = array())
    {
        try {
            //
            $arrOrder = array();
            if ($payload['select_shipper'] !== -1) {
                $userAssign = $this->_usersInterface->getById($payload['select_shipper']);

                if (!$userAssign) {
                    throw new \Exception('Không tìm thấy user');
                }

                if (!$userAssign->hasRole('shipper')) {
                    throw new \Exception('Vai trò không khả dụng');
                }
            } else {
                $userAssign = true;
            }

            if (count($orders) === 0) {
                throw new \Exception('Không có đơn hàng');
            }

            DB::beginTransaction();

            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 2;
                    $order->status_detail = 23;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    if ($userAssign !== true) {
                        // xoa luot giao cua shipper cu
                        $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                            'shop_id' => $order->shop_id,
                            'order_id' => $order_id,
                            'status'        => 2,
                            'status_detail' => 23
                        ));
                        if ($orderShipAssigned) {
                            $orderShipAssigned->delete();
                        }

                        $orderShipAssigned = $this->_orderShipAssignedInterface->create([
                            'user_role'     => 'shipper',
                            'user_id'       => $payload['select_shipper'],
                            'shop_id'       => $order->shop_id,
                            'order_id'      => $order_id,
                            'sender_id'     => $order->sender_id,
                            'p_id'          => $order->sender->p_id,
                            'd_id'          => $order->sender->d_id,
                            'w_id'          => $order->sender->w_id,
                            'status'        => 2,
                            'status_detail' => 23,
                            'failed_status' => 23,
                            'time_assigned' => (int)date('Ymd')
                        ]);

                        //Chốt thời gian quá hạn lưu kho
                        $orderStatusOvertime = $this->_orderStatusOvertimeInterface->getOne(array(
                            'order_id'          => $order_id,
                            'status'            => 2,
                            'status_detail'     => 25,
                            'end_date_null' => true,
                        ));
                        if ($orderStatusOvertime) {
                            $orderStatusOvertime->end_date = (int)date('Ymd');
                            $orderStatusOvertime->save();
                        }

                        //Thêm dữ liệu log
                        $log_data[] = [
                            'model' => $orderShipAssigned,
                        ];

                        if (!$this->_orderFeeInterface->checkExist(array(
                            'shop_id'   => $order->shop_id,
                            'order_id'  => $order->id,
                            'fee_type'  => 'refund_transport'
                        ))) {
                            $created_date = (int)Carbon::now()->format('Ymd');
                            $arrFee = array(
                                array(
                                    'shop_id' => $order->shop_id,
                                    'order_id' => $order->id,
                                    'fee_type' => 'refund_transport',
                                    'date' => $created_date,
                                    'value' => 0
                                ),
                            );
                            $this->_orderFeeInterface->insert($arrFee);
                        }
                    } else {
                        $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                            'shop_id' => $order->shop_id,
                            'order_id' => $order_id,
                            'status'        => 2,
                            'status_detail' => 23
                        ));
                        if ($orderShipAssigned) {
                            $orderShipAssigned->time_success = 0;
                            $orderShipAssigned->time_failed = 0;
                            $orderShipAssigned->failed_status = 23;
                            $orderShipAssigned->save();
                        }

                        //Chốt thời gian quá hạn duyệt hoàn
                        $orderStatusOvertime = $this->_orderStatusOvertimeInterface->getOne(array(
                            'order_id'          => $order_id,
                            'status'            => 3,
                            'status_detail'     => 34,
                            'end_date_null' => true,
                        ));
                        if ($orderStatusOvertime) {
                            $orderStatusOvertime->end_date = (int)date('Ymd');
                            $orderStatusOvertime->save();
                        }
                    }

                    $note2 = ($userAssign === true) ? 'Shipper giao lại hàng' : 'Gán shipper giao hàng: ' . $userAssign->name;
                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'assign_shipper_send',
                        'status'    => 2,
                        'status_detail' => 23,
                        'note1'     => 'Đang giao hàng',
                        'note2'     => $note2,
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));

                } else {
                    throw new \Exception('Dữ liệu không chính xác');
                }
            }

            $shipperId = (int)$payload['select_shipper'];
            if ($shipperId != -1) {
                $countOrders = count($orders);
                $payloadNoti = array(
                    'sender_id' => $payload['user_id'],
                    'user_id' => $shipperId,
                    'content_data' => array(
                        2, $countOrders
                    )
                );
                $this->_notificationServices->sendToUser($payloadNoti);
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function cancelOrder($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order_id = (int) $order_id;
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 6;
                    $order->status_detail = 61;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id
                    ), array(
                        'orderBy' => 'id'
                    ));
                    if ($orderShipAssigned) {
                        $orderShipAssigned->failed_status = 61;
                        $orderShipAssigned->deleted_at = now();
                        $orderShipAssigned->save();
                    }
//                    $orderShipAssigned = $this->_orderShipAssignedInterface->delByCond(
//                        array(
//                            'order_id' => $order_id,
//                            'shop_id' => $order->shop_id,
//                        )
//                    );

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'cancel',
                        'status'    => 6,
                        'status_detail' => 61,
                        'note1'     => 'Huỷ đơn',
                        'note2'     => $payload['cancel_note'],
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));

                    //Lưu thông báo hủy đơn hàng về shop
                    $notificationPayload = array(
                        'sender_id' => $payload['user_id'],
                        'shop_id' => $order->shop_id,
                        'type' => 2,
                        'content_data' => array(
                            4, $order->lading_code
                        )
                    );
                    $this->_notificationServices->sendToShop($notificationPayload);
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function sendFail($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 2;
                    $order->status_detail = 24;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id,
                        'status'        => 2,
                        'status_detail' => 23
                    ));
                    if ($orderShipAssigned) {
                        $orderShipAssigned->time_success = 0;
                        $orderShipAssigned->time_failed = (int)date('Ymd');
                        $orderShipAssigned->failed_status = 23;
                        $orderShipAssigned->save();
                    }

                    //Thêm dữ liệu log
                    $log_data[] = [
                        'model' => $orderShipAssigned,
                    ];

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'send_fail',
                        'status'    => 2,
                        'status_detail' => 24,
                        'note1'     => 'Giao hàng thất bại',
                        'note2'     => $payload['fail_note'],
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function setRefund($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 3;
                    $order->status_detail = 31;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    //Chốt thời gian quá hạn lưu kho
                    $orderStatusOvertime = $this->_orderStatusOvertimeInterface->getOne(array(
                        'order_id'          => $order_id,
                        'status'            => 2,
                        'status_detail'     => 25,
                        'end_date_null' => true,
                    ));
                    if ($orderStatusOvertime) {
                        $orderStatusOvertime->end_date = (int)date('Ymd');
                        $orderStatusOvertime->save();
                    }

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'set_refund',
                        'status'    => 3,
                        'status_detail' => 31,
                        'note1'     => 'Chuyển hoàn',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );
                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function pickFail($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 1;
                    $order->status_detail = 13;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id,
                        'status'        => 1,
                        'status_detail' => 12
                    ));
                    if ($orderShipAssigned) {
                        $orderShipAssigned->time_success = 0;
                        $orderShipAssigned->time_failed = (int)date('Ymd');
                        $orderShipAssigned->failed_status = 13;
                        $orderShipAssigned->save();
                    }

                    //Thêm dữ liệu log
                    $log_data[] = [
                        'model' => $orderShipAssigned,
                    ];

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'pick_fail',
                        'status'    => 1,
                        'status_detail' => 13,
                        'note1'     => 'Lấy hàng thất bại',
                        'note2'     => $payload['fail_note'],
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function sendSuccess($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 5;
                    $order->status_detail = 51;
                    $order->send_success_date = (int)date('Ymd');
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    // OK
                    $orderShipAssigned = $this->_orderShipAssignedInterface->updateByCondition(
                        array(
                            'shop_id' =>  $order->shop_id,
                            'order_id' => $order_id,
                            'status'        => 2,
                            'status_detail' => 23
                        ),
                        array(
                            'time_success' => (int)date('Ymd'),
                            'time_failed' => 0,
                            'failed_status' => 51
                        ),
                        array(
                            'orderBy' => 'id'
                        )
                    );

                    //Thêm dữ liệu log
                    $log_data[] = [
                        'model' => $orderShipAssigned,
                    ];

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'send_success',
                        'status'    => 5,
                        'status_detail' => 51,
                        'note1'     => 'Giao hàng thành công',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );
                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));

                    //Lưu thông báo giao hàng thành công về shop
                    $notificationPayload = array(
                        'sender_id' => $payload['user_id'],
                        'shop_id' => $order->shop_id,
                        'type' => 1,
                        'content_data' => array(
                            3, $order->lading_code
                        )
                    );
                    $this->_notificationServices->sendToShop($notificationPayload);
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;

        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function refundSuccess($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 5;
                    $order->status_detail = 52;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id,
                        'status'        => 3,
                        'status_detail' => 32
                    ));
                    if ($orderShipAssigned) {
                        $orderShipAssigned->time_success = (int)date('Ymd');
                        $orderShipAssigned->time_failed = 0;
                        $orderShipAssigned->failed_status = 52;
                        $orderShipAssigned->save();
                    }

                    //Thêm dữ liệu log
                    $log_data[] = [
                        'model' => $orderShipAssigned,
                    ];

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'refund_success',
                        'status'    => 5,
                        'status_detail' => 52,
                        'note1'     => 'Hoàn hàng thành công',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function confirmResend($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                     //Thêm dữ liệu log
                     $log_data = [];
                     $log_data[] = [
                         'old_data' => $order,
                     ];

                    $order->status = 4;
                    $order->status_detail = 41;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    if ($this->_orderShipAssignedInterface->checkExist(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id,
                        'status'        => 2,
                        'status_detail' => 23
                    ))) {
                        $this->_orderShipAssignedInterface->updateByCondition(
                            array(
                                'shop_id' => $order->shop_id,
                                'order_id' => $order_id,
                                'status'        => 2,
                                'status_detail' => 23
                            ),
                            array(
                                'time_success' => 0,
                                'time_failed'  => (int)date('Ymd'),
                                'failed_status'  => 41,
                            ),
                            array(),
                            true
                        );
                    }

                    //Chốt thời gian quá hạn lưu kho
                    $orderStatusOvertime = $this->_orderStatusOvertimeInterface->getOne(array(
                        'order_id'          => $order_id,
                        'status'            => 2,
                        'status_detail'     => 25,
                        'end_date_null' => true,
                    ));
                    if ($orderStatusOvertime) {
                        $orderStatusOvertime->end_date = (int)date('Ymd');
                        $orderStatusOvertime->save();
                    }

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'confirm_resend',
                        'status'    => 4,
                        'status_detail' => 41,
                        'note1'     => 'Chờ xác nhận giao lại',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );
                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function confirmRefund($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 3;
                    $order->status_detail = 34;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id,
                        'status'        => 2,
                        'status_detail' => 23,
                        'time_failed' => true
                    ));
                    if ($orderShipAssigned) {
                        $orderShipAssigned->time_success = 0;
                        $orderShipAssigned->time_failed = (int)date('Ymd');
                        $orderShipAssigned->failed_status = 34;
                        $orderShipAssigned->save();
                    }

                    //Lưu cảnh báo quá hạn chờ duyệt hoàn
                    $this->_orderStatusOvertimeInterface->create([
                        'order_id'          => $order_id,
                        'shop_id'           => $order->shop_id,
                        'status'            => 3,
                        'status_detail'     => 34,
                        'start_date'       => (int)date('Ymd')
                    ]);

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'confirm_refund',
                        'status'    => 3,
                        'status_detail' => 34,
                        'note1'     => 'Chờ duyệt hoàn',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );
                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function approvalRefund($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 3;
                    $order->status_detail = 35;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id,
                        'status'        => 2,
                        'status_detail' => 23,
                        'time_failed' => true
                    ));
                    if ($orderShipAssigned) {
                        $orderShipAssigned->failed_status = 35;
                        $orderShipAssigned->save();
                    }

                    //Chốt thời gian quá hạn duyệt hoàn
                    $orderStatusOvertime = $this->_orderStatusOvertimeInterface->getOne(array(
                        'order_id'          => $order_id,
                        'status'            => 3,
                        'status_detail'     => 34,
                        'end_date_null'      => true
                    ));
                    if ($orderStatusOvertime) {
                        $orderStatusOvertime->end_date = (int)date('Ymd');
                        $orderStatusOvertime->save();
                    }

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'approval_refund',
                        'status'    => 3,
                        'status_detail' => 35,
                        'note1'     => 'Duyệt hoàn',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );
                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function pickSuccess($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 2;
                    $order->status_detail = 21;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'pick_success',
                        'status'    => 2,
                        'status_detail' => 21,
                        'note1'     => 'Lấy hàng thành công',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function store($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 2;
                    $order->status_detail = 25;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $note1 = 'Hàng lưu kho';
                    $note2 = '';
                    if (isset($payload['post_office'])) {
                        $postOffices = $this->_postOfficesInterface->getById((int)$payload['post_office']);
                        if ($postOffices) {
                            $note1 = 'Hàng lưu tại ' . $postOffices->name;
                            $note2 = $note1;
                        }

                        if (isset($payload['select_user_receiver'])) {
                            $user = User::find((int)$payload['select_user_receiver']);
                            if ($user) {
                                $note2 .= '. Nhân viên kho: ' . $user->name;
                            }
                        }
                    }

                    //Lưu cảnh báo quá hạn lưu kho
                    $this->_orderStatusOvertimeInterface->create([
                        'order_id'          => $order_id,
                        'shop_id'           => $order->shop_id,
                        'status'            => 2,
                        'status_detail'     => 25,
                        'start_date'     => (int)date('Ymd')
                    ]);

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'store',
                        'status'    => 2,
                        'status_detail' => 25,
                        'note1'     => $note1,
                        'note2'     => $note2,
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function wareHouse($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
//                $order = $this->_ordersInterface->getById($order_id);
                $order = $this->_ordersInterface->getOne(array('id' => $order_id), array(
                    'with' => array('shop', 'extra', 'sender.provinces', 'receiver.provinces')
                ));
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 2;
                    $order->status_detail = 22;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    // elastic search
                    $order->addToIndex();

                    if (!$this->_orderShipAssignedInterface->checkExist(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id,
                        'status'        => 1,
                        'status_detail' => 12,
                        'time_success' => true
                    ))) {
                        $this->_orderShipAssignedInterface->updateByCondition(
                            array(
                                'shop_id' => $order->shop_id,
                                'order_id' => $order_id,
                                'status'        => 1,
                                'status_detail' => 12
                            ),
                            array(
                                'time_success' => (int)date('Ymd'),
                                'time_failed'  => 0,
                                'failed_status'  => 22,
                            )
                        );
                    }

                    // phi thu cod
                    if (!$this->_orderFeeInterface->checkExist(array(
                        'shop_id'   => $order->shop_id,
                        'order_id'  => $order->id,
                        'fee_type'  => 'cod'
                    ))) {
                        $cod_fee = 0;
                        $check_cod_fee = $this->_orderSettingCodInterface->getOne(array('cod' => $order->cod));
                        if ($check_cod_fee) {
                            // percent | money
                            if ($check_cod_fee->type === 'money')
                                $cod_fee = $check_cod_fee->value;
                            else
                                $cod_fee = ($check_cod_fee->value / 100) * $order->cod;
                        }
                        if ($cod_fee > 0) {
                            $created_date = (int) Carbon::now()->format('Ymd');
                            $arrFee = array(
                                array(
                                    'shop_id'   => $order->shop_id,
                                    'order_id'  => $order->id,
                                    'fee_type'  => 'cod',
                                    'date'      => $created_date,
                                    'value'     => $cod_fee
                                ),
                            );
                            $this->_orderFeeInterface->insert($arrFee);
                            //Thêm dữ liệu log
                            $log_data[] = [
                                'model' => $this->_orderFeeInterface,
                            ];
                        }
                    }

                    if ($order->payfee === 'payfee_sender') {
                        if (!$this->_orderFeeInterface->checkExist(array(
                            'shop_id'   => $order->shop_id,
                            'order_id'  => $order->id,
                            'fee_type'  => 'transport'
                        ))) {
                            // tinh phi van chuyen
                            $sendAddress = $this->_shopAddressInterface->getById($order->sender_id);
                            $payloadFee = array(
                                'p_id_send' => $sendAddress->p_id,
                                'p_id_receive' => $order->receiver->p_id,
                                'd_id_receive' => $order->receiver->d_id,
                                'service' => $order->service_type,
                                'weight' => $order->weight
                            );
                            $check_transport_fee = $this->_calculatorFee->calculatorFee($payloadFee);
                            if (!$check_transport_fee->status) {
                                throw new \Exception('Dữ liệu không chính xác');
                            }

                            // phi van chuyen
                            $transport_fee = $check_transport_fee->result;
                            $created_date = (int) Carbon::now()->format('Ymd');
                            $arrFee = array(
                                array(
                                    'shop_id'   => $order->shop_id,
                                    'order_id'  => $order->id,
                                    'fee_type'  => 'transport',
                                    'date'      => $created_date,
                                    'value'     => $transport_fee
                                ),
                            );
                            $this->_orderFeeInterface->insert($arrFee);

                            //Thêm dữ liệu log
                            $log_data[] = [
                                'model' =>  $this->_orderFeeInterface,
                            ];
                        }
                    }

                    // phi bao hiem
                    $check_insurance_fee = $this->_orderSettingFeeInsuranceInterface->getOne(array('insurance' => $order->insurance_value));
                    $insurance_fee = ($check_insurance_fee) ? $check_insurance_fee->value : 0;
                    if ($insurance_fee > 0) {
                        if (!$this->_orderFeeInterface->checkExist(array(
                            'shop_id'   => $order->shop_id,
                            'order_id'  => $order->id,
                            'fee_type'  => 'insurance'
                        ))) {
                            $created_date = (int)Carbon::now()->format('Ymd');
                            $arrFee = array(
                                array(
                                    'shop_id' => $order->shop_id,
                                    'order_id' => $order->id,
                                    'fee_type' => 'insurance',
                                    'date' => $created_date,
                                    'value' => $insurance_fee
                                ),
                            );
                            $this->_orderFeeInterface->insert($arrFee);

                            //Thêm dữ liệu log
                            $log_data[] = [
                                'model' =>  $this->_orderFeeInterface,
                            ];
                        }
                    }

                    $note1 = 'Hàng đã nhập bưu cục';
                    $note2 = '';
                    if (isset($payload['post_office'])) {
                        $postOffices = $this->_postOfficesInterface->getById((int)$payload['post_office']);
                        if ($postOffices) {
                            $note1 = 'Hàng đã nhập ' . $postOffices->name;
                            $note2 = $note1;
                        }

                        if (isset($payload['select_user_receiver'])) {
                            $user = User::find((int)$payload['select_user_receiver']);
                            if ($user) {
                                $note2 .= '. Nhân viên kho: ' . $user->name;
                            }
                        }
                    }

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'warehouse',
                        'status'    => 2,
                        'status_detail' => 22,
                        'note1'     => $note1,
                        'note2'     => $note2,
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function wareHouseRefund($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 3;
                    $order->status_detail = 36;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id,
                        'status'        => 2,
                        'status_detail' => 23,
                        'time_failed' => true
                    ));
                    if ($orderShipAssigned) {
                        $orderShipAssigned->failed_status = 36;
                        $orderShipAssigned->save();
                    }

                    $note1 = 'Hàng đã hoàn về bưu cục';
                    $note2 = '';
                    if (isset($payload['post_office'])) {
                        $postOffices = $this->_postOfficesInterface->getById((int)$payload['post_office']);
                        if ($postOffices) {
                            $note1 = 'Hàng đã hoàn về ' . $postOffices->name;
                            $note2 = $note1;
                        }

                        if (isset($payload['select_user_receiver'])) {
                            $user = User::find((int)$payload['select_user_receiver']);
                            if ($user) {
                                $note2 .= '. Nhân viên kho: ' . $user->name;
                            }
                        }
                    }

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'warehouse_refund',
                        'status'    => 3,
                        'status_detail' => 36,
                        'note1'     => $note1,
                        'note2'     => $note2,
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function missing($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    if ($order->status_detail === 23) {
                        $this->_orderShipAssignedInterface->updateByCondition(
                            array(
                                'shop_id' => $order->shop_id,
                                'order_id' => $order_id,
                                'status'        => 2,
                                'status_detail' => 23
                            ),
                            array(
                                'time_success' => 0,
                                'time_failed' => (int)date('Ymd'),
                                'failed_status' => 71
                            )
                        );
                    } elseif ($order->status_detail === 32) {
                        $this->_orderShipAssignedInterface->updateByCondition(
                            array(
                                'shop_id' => $order->shop_id,
                                'order_id' => $order_id,
                                'status'        => 3,
                                'status_detail' => 32
                            ),
                            array(
                                'time_success' => 0,
                                'time_failed' => (int)date('Ymd'),
                                'failed_status' => 71
                            )
                        );
                    }

                    $order->status = 7;
                    $order->status_detail = 71;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'missing',
                        'status'    => 7,
                        'status_detail' => 71,
                        'note1'     => 'Hàng thất lạc',
                        'note2'     => $payload['missing_note'],
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function missingConfirm($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 7;
                    $order->status_detail = 73;
                    $order->last_change_date = (int)date('Ymd');
                    $order->collect_money_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    //tien boi thuong
                    if (!$this->_orderFeeInterface->checkExist(array(
                        'shop_id'   => $order->shop_id,
                        'order_id'  => $order->id,
                        'fee_type'  => 'incurred_money_indemnify'
                    ))) {
                        $created_date = (int) Carbon::now()->format('Ymd');
                        $arrFee = array(
                            array(
                                'shop_id'   => $order->shop_id,
                                'order_id'  => $order_id,
                                'fee_type'  => 'incurred_money_indemnify',
                                'date'      => $created_date,
                                'value'     => $payload['missing_confirm_indemnify']
                            )
                        );
                        $this->_orderFeeInterface->insert($arrFee);
                        //Thêm dữ liệu log
                        $log_data[] = [
                            'model' => $this->_orderFeeInterface,
                        ];
                    }

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'missing_confirm',
                        'status'    => 7,
                        'status_detail' => 73,
                        'note1'     => 'Đã thoả thuận thất lạc',
                        'note2'     => 'Số tiền bồi thường: ' . number_format($payload['missing_confirm_indemnify']) . 'vnđ. ' . $payload['missing_confirm_note'],
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function damaged($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    if ($order->status_detail === 23) {
                        $this->_orderShipAssignedInterface->updateByCondition(
                            array(
                                'shop_id' => $order->shop_id,
                                'order_id' => $order_id,
                                'status'        => 2,
                                'status_detail' => 23
                            ),
                            array(
                                'time_success' => 0,
                                'time_failed' => (int)date('Ymd'),
                                'failed_status' => 72
                            )
                        );
                    } elseif ($order->status_detail === 32) {
                        $this->_orderShipAssignedInterface->updateByCondition(
                            array(
                                'shop_id' => $order->shop_id,
                                'order_id' => $order_id,
                                'status'        => 3,
                                'status_detail' => 32
                            ),
                            array(
                                'time_success' => 0,
                                'time_failed' => (int)date('Ymd'),
                                'failed_status' => 72
                            )
                        );
                    }

                    $order->status = 7;
                    $order->status_detail = 72;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'damaged',
                        'status'    => 7,
                        'status_detail' => 72,
                        'note1'     => 'Hàng hư hỏng',
                        'note2'     => $payload['damaged_note'],
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function damagedConfirm($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 7;
                    $order->status_detail = 74;
                    $order->last_change_date = (int)date('Ymd');
                    $order->collect_money_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    //tien boi thuong
                    if (!$this->_orderFeeInterface->checkExist(array(
                        'shop_id'   => $order->shop_id,
                        'order_id'  => $order->id,
                        'fee_type'  => 'incurred_money_indemnify'
                    ))) {
                        $created_date = (int) Carbon::now()->format('Ymd');
                        $arrFee = array(
                            array(
                                'shop_id'   => $order->shop_id,
                                'order_id'  => $order_id,
                                'fee_type'  => 'incurred_money_indemnify',
                                'date'      => $created_date,
                                'value'     => $payload['damaged_confirm_indemnify']
                            )
                        );
                        $this->_orderFeeInterface->insert($arrFee);
                        //Thêm dữ liệu log
                        $log_data[] = [
                            'model' => $this->_orderFeeInterface,
                        ];
                    }

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'damaged_confirm',
                        'status'    => 7,
                        'status_detail' => 74,
                        'note1'     => 'Đã thoả thuận hư hỏng',
                        'note2'     => 'Số tiền bồi thường: ' . number_format($payload['damaged_confirm_indemnify']) . 'vnđ. ' . $payload['damaged_confirm_note'],
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function collectMoney($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 9;
                    $order->status_detail = 91;
                    $order->collect_money_date = (int)date('Ymd');
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $this->_orderShipAssignedInterface->updateByCondition(
                        array(
                            'shop_id' => $order->shop_id,
                            'order_id' => $order_id,
                            'status'        => 2,
                            'status_detail' => 23,
                            'failed_status' => 51
                        ),
                        array(
                            'failed_status' => 91
                        )
                    );

                    //total_cod
                    if (!$this->_orderFeeInterface->checkExist(array(
                        'shop_id'   => $order->shop_id,
                        'order_id'  => $order->id,
                        'fee_type'  => 'total_cod'
                    ))) {
                        $created_date = (int) Carbon::now()->format('Ymd');
                        $arrFee = array(
                            array(
                                'shop_id'   => $order->shop_id,
                                'order_id'  => $order_id,
                                'fee_type'  => 'total_cod',
                                'date'      => $created_date,
                                'value'     => $order->cod
                            )
                        );
                        $this->_orderFeeInterface->insert($arrFee);
                        //Thêm dữ liệu log
                        $log_data[] = [
                            'model' => $this->_orderFeeInterface,
                        ];
                    }

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'collect_money',
                        'status'    => 9,
                        'status_detail' => 91,
                        'note1'     => 'Chờ đối soát',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function reconcileSend($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 8;
                    $order->status_detail = 81;
                    $order->reconcile_send_date = (int)date('Ymd');
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'reconcile_send',
                        'status'    => 8,
                        'status_detail' => 81,
                        'note1'     => 'Đối soát giao hàng',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function reconcileRefund($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 8;
                    $order->status_detail = 82;
                    $order->reconcile_refund_date = (int)date('Ymd');
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $orderShipAssigned = $this->_orderShipAssignedInterface->getOne(array(
                        'shop_id' => $order->shop_id,
                        'order_id' => $order_id,
                        'status'        => 3,
                        'status_detail' => 32
                    ));
                    if ($orderShipAssigned) {
                        $orderShipAssigned->time_success = (int)date('Ymd');
                        $orderShipAssigned->time_failed = 0;
                        $orderShipAssigned->failed_status = 82;
                        $orderShipAssigned->save();
                    }

                    if (!$this->_orderFeeInterface->checkExist(array(
                        'shop_id'   => $order->shop_id,
                        'order_id'  => $order->id,
                        'fee_type'  => 'refund_cod'
                    ))) {
                        $orderFee = $this->_orderFeeInterface->getOne(array(
                            'shop_id'   => $order->shop_id,
                            'order_id'  => $order->id,
                            'fee_type'  => 'cod'
                        ));
                        if ($orderFee) {
                            $created_date = (int)Carbon::now()->format('Ymd');
                            $arrFee = array(
                                array(
                                    'shop_id' => $order->shop_id,
                                    'order_id' => $order->id,
                                    'fee_type' => 'refund_cod',
                                    'date' => $created_date,
                                    'value' => $orderFee->value
                                ),
                            );
                            $this->_orderFeeInterface->insert($arrFee);
                        }
                    }

                    $orderFeeTransport = $this->_orderFeeInterface->getOne(array(
                        'shop_id'   => $order->shop_id,
                        'order_id'  => $order->id,
                        'fee_type'  => 'refund_transport'
                    ));
                    if (!$orderFeeTransport) {
                        $orderFee = $this->_orderFeeInterface->getOne(array(
                            'shop_id'   => $order->shop_id,
                            'order_id'  => $order->id,
                            'fee_type'  => 'transport'
                        ));
                        if ($orderFee) {
                            $created_date = (int)Carbon::now()->format('Ymd');
                            $arrFee = array(
                                array(
                                    'shop_id' => $order->shop_id,
                                    'order_id' => $order->id,
                                    'fee_type' => 'refund_transport',
                                    'date' => $created_date,
                                    'value' => $orderFee->value
                                ),
                            );
                            $this->_orderFeeInterface->insert($arrFee);
                        }
                    }

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'reconcile_refund',
                        'status'    => 8,
                        'status_detail' => 82,
                        'note1'     => 'Đối soát hoàn hàng',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function reconcileMissing($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 8;
                    $order->status_detail = 83;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'reconcile_missing',
                        'status'    => 8,
                        'status_detail' => 83,
                        'note1'     => 'Đối soát thất lạc',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    public function reconcileDamaged($orders = array(), $payload = array())
    {
        try {
            DB::beginTransaction();

            //
            $arrOrder = array();
            foreach ($orders as $order_id) {
                $order = $this->_ordersInterface->getById($order_id);
                if ($order) {
                    //Thêm dữ liệu log
                    $log_data = [];
                    $log_data[] = [
                        'old_data' => $order,
                    ];

                    $order->status = 8;
                    $order->status_detail = 84;
                    $order->last_change_date = (int)date('Ymd');
                    $order->save();
                    $arrOrder[] = $order;

                    $dataLog = array(
                        'order_id'  => $order_id,
                        'user_type' => $payload['user_type'],
                        'user_id'   => $payload['user_id'],
                        'log_type'  => 'reconcile_damaged',
                        'status'    => 8,
                        'status_detail' => 84,
                        'note1'     => 'Đối soát hư hỏng',
                        'note2'     => '',
                        'logs'      => json_encode($payload),
                        'timer'     => now()
                    );

                    $createOrderLog = $this->createOrderLog($dataLog);

                    //Lưu log
                    event(new CreateLogEvents($log_data, 'orders', 'orders_update', $order_id));
                }
            }

            DB::commit();

            $dataRes = new \stdClass();
            $dataRes->result = true;
            $dataRes->orders = $arrOrder;
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }

    /*
     * Order logs
     */
    public function createOrderLog($payload = array())
    {
        $validator = Validator::make($payload, [
            'order_id' => 'required|numeric', //|exists:orders,id
            'user_type' => 'required|in:user,shop',
            'status_detail' => 'required|numeric',
            'status' => 'required|numeric',
            'user_id' => 'required|numeric',
            'log_type' => 'required',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors());
        }

        return $this->_orderLogInterface->create(array(
            'order_id'          => $payload['order_id'],
            'user_type'         => $payload['user_type'],
            'user_id'           => $payload['user_id'],
            'status'            => $payload['status'],
            'status_detail'     => $payload['status_detail'],
            'note1'             => $payload['note1'],
            'note2'             => $payload['note2'],
            'log_type'          => $payload['log_type'],
            'logs'              => $payload['logs'],
            'timer'             => now()
        ));
    }

    /*
     * Delete cookie draft, update cookie
     */
    public function updateCookieDraft($keyRedis = '', $draftList = array())
    {
        if (count($draftList)) {
            for ($i = 0; $i <= count($draftList); $i++) {
                if (isset($_COOKIE[$keyRedis . '_' . $i])) {
                    Redis::lset($keyRedis, $i - 1, $_COOKIE[$keyRedis . '_' . $i]);
                    setcookie($keyRedis . '_' . $i, null, -1, '/');
                }
            }
        }
    }

    /*
     * Gen key draft orders by User
     */
    public function genDraft($keyRedis)
    {
        $draftList = Redis::lrange($keyRedis, 0, -1);
        $keyRedisDraft = $keyRedis . '_' . (count($draftList) + 1);
        if (isset($_COOKIE[$keyRedisDraft])) {
            Redis::rpush($keyRedis, $_COOKIE[$keyRedisDraft]);
        }
        return $draftList;
    }

    public function filterDraft($draftList = array())
    {
        return collect($draftList)->map(function ($draft, $key) {
            $draft = json_decode($draft);
            if ($draft) {
                $draft->process = 20;
                if ($draft->quantity_products) {
                    if ((int)$draft->quantity_products === count($draft->addProductName)) {
                        if (collect($draft->addProductName)->filter()->count() === count($draft->addProductName)) {
                            if (collect($draft->addProductPrice)->filter()->count() === count($draft->addProductPrice)) {
                                if (collect($draft->addProductSlg)->filter()->count() === count($draft->addProductSlg)) {
                                    $draft->process = 100;
                                }
                            }
                        }
                    }
                }
                return $draft;
            }
        })->filter()->all();
    }

    public function getByShipper($request, &$filter, $userId, &$viewFile, &$ordersList, &$arrStatusDetail, &$countStatus, &$statusActive)
    {
        $you = auth('admin')->user();
        $user_roles = $you->getRoleNames()->toArray();
        $statusShipper = array();
        if (in_array('shipper', $user_roles)) {
            $status = array_keys(OrderConstant::statusShipperShip)[0];
            $statusShipper = $statusShipper + OrderConstant::statusShipperShip;
        }
        if (in_array('refund', $user_roles)) {
            $status = array_keys(OrderConstant::statusShipperRefund)[0];
            $statusShipper = $statusShipper + OrderConstant::statusShipperRefund;
        }
        if (in_array('pickup', $user_roles)) {
            $status = array_keys(OrderConstant::statusShipperPickup)[0];
            $statusShipper = $statusShipper + OrderConstant::statusShipperPickup;
        }
        $status_detail = 0;
        $status = $request->input('status', $status);
        $statusShipperMerge = array();
        if (in_array('shipper', $user_roles)) {
            if (isset(OrderConstant::statusShipperShip[$status]))
                $statusShipperMerge += array_keys(OrderConstant::statusShipperShip[$status]['detail']);
        }
        if (in_array('refund', $user_roles)) {
            if (isset(OrderConstant::statusShipperRefund[$status]))
                $statusShipperMerge += array_keys(OrderConstant::statusShipperRefund[$status]['detail']);
        }
        if (in_array('pickup', $user_roles)) {
            if (isset(OrderConstant::statusShipperPickup[$status]))
                $statusShipperMerge += array_keys(OrderConstant::statusShipperPickup[$status]['detail']);
        }

        $user_role = $user_roles[0];
        $status_detail = $statusShipperMerge[0];
        if ($status === 3 || $status === 8) {
            if (in_array('refund', $user_roles)) {
                $user_role = 'refund';
            }
        }

//        if ($user_role === 'shipper') {
//            if (OrderConstant::statusShipperShip[$status]['detail']) {
//                $status_detail = array_keys(OrderConstant::statusShipperShip[$status]['detail'])[0];
//            }
//        }
//        if ($user_role === 'refund') {
//            if (OrderConstant::statusShipperRefund[$status]['detail']) {
//                $status_detail = array_keys(OrderConstant::statusShipperRefund[$status]['detail'])[0];
//            }
//        }
//        if ($user_role === 'pickup') {
//            if (OrderConstant::statusShipperPickup[$status]['detail']) {
//                $status_detail = array_keys(OrderConstant::statusShipperPickup[$status]['detail'])[0];
//            }
//        }
        $status_detail = (int)$request->input('status_detail', $status_detail);
        $filter['status'] = $status;
        $filter['status_detail'] = $status_detail;
        $statusActive = $status;

        $endDate = date('Ymd');
        $beginDate = date('Ymd', strtotime('-14 day'));

        if ($request->has('created_from') && $request->has('created_to')) {
            $beginDate = date('Ymd', strtotime($request->input('created_from')));
            $endDate = date('Ymd', strtotime($request->input('created_to')));
        }

        if ($request->has('begin') && $request->has('end')) {
            $beginDate = date('Ymd', strtotime($request->input('begin')));
            $endDate = date('Ymd', strtotime($request->input('end')));
        }

        $filter['created_range'] = array((int)$beginDate, (int)$endDate);
        if (!isset($statusShipper[$status])) {
            abort(404);
        }

        $arrStatusDetail = $statusShipper[$status]['detail'];
        $status_detail_shipper = array_keys($arrStatusDetail);
        $filter['status_detail_shipper'] = $status_detail_shipper;
        $filter['assignUserId'] = $userId;
        $filter['assignUserRole'] = $user_role;
        $filter['assignStatus'] = $status_detail;
        $filter['join'] = true;

        $status_detail_shipper_all = array();
        foreach ($statusShipper as $status) {
            $status_detail_shipper_all = array_merge($status_detail_shipper_all, array_keys($status['detail']));
        }
        $countStatus = $this->countByStatus(0,
            array(
                'join' => true,
                'assignUserId' => $userId,
                'assignUserRole' => $user_roles,
                'status_detail_shipper' => $status_detail_shipper_all
            )
        );
        $limit = $request->input('limit', config('options.limit'));
        $condition = array();
        $condition['getByShipper_user_id'] = $userId;
        $condition['getByShipper_user_role'] = $user_role;
        $condition['created_range'] = array((int)$beginDate, (int)$endDate);
        $condition['getByShipper_status_detail'] = $status_detail ?? $status_detail_shipper;
        $ordersList = $this->_ordersInterface->getMore(
            $condition,
            array(
                'with' => array('shop', 'extra', 'sender.provinces', 'receiver.provinces'),
                'orderBy' => array('status_detail', 'updated_at'),
                'direction' => array('ASC')
            ), $limit);
        $filter['limit'] = $limit;

        $viewFile = 'Orders::orders.shipper.list';
        if ( Agent::isMobile() || Agent::isTablet() || Agent::isPhone() ) {
            $viewFile = 'Orders::orders.shipper.mobile.list';
        }
        return;
    }

    public function getByShop($request, $userId, &$filter, &$countDraft, &$countStatus, &$shop)
    {
        $shopId = $request->input('shop');
        $shop = $this->_shopsInterface->getById($shopId);
        if (!$shop) {
            return abort(404);
        }
        if (count($request->all()) === 1) {
            $keyRedis = ':admin:'.$userId.':selected_shop';
            $draftList = Redis::lrange($keyRedis, 0, -1);
            if (count($draftList) > 0) {
                if ($draftList[0] !== $shopId) Redis::lpush($keyRedis, $shopId);
            } else {
                Redis::lpush($keyRedis, $shopId);
            }
        }
        $filter['shop_id'] = $shopId;
        /*
         * Gen key draft orders by User
         */
        $keyRedis = ':admin:'.$userId.':draft_order:shop_'.$shopId;
        $draftList = Redis::lrange($keyRedis, 0, -1);
        $keyRedisDraft = $keyRedis . '_' . (count($draftList) + 1);
        if (isset($_COOKIE[$keyRedisDraft])) {
            Redis::rpush($keyRedis, $_COOKIE[$keyRedisDraft]);
        }

        /*
         * Delete cookie draft, update cookie
         */
        $this->updateCookieDraft($keyRedis, $draftList);

        /*
         * Get draft data
         */
        $keyRedis = ':admin:'.$userId.':draft_order:shop_'.$shopId;
        $draftList = Redis::lrange($keyRedis, 0, -1);
        $draftList = $this->filterDraft($draftList);
        $countDraft = count($draftList);

        /*
         * Count order by status
         */
        $countStatus = $this->countByStatus($shopId);
    }

    public function generateLandingCode()
    {
        // B01022199981
        $date = (int)date('Ymd');
        $keyRedis = ':orders:count_by_date:'.$date;
        $countOrders = Redis::get($keyRedis);
        if (is_null($countOrders)) {
            $countOrders = $this->_ordersInterface->checkExist(array('created_date' => $date));
            Redis::set($keyRedis, $countOrders);
        } else {
            $countOrders = $countOrders + 1;
            Redis::set($keyRedis, $countOrders);
        }
        $timer = date('dm');
        $randInt = rand(100, 999);
        $getNum = 9999 - $countOrders;
        $prefix = config('order.prefix_lading_code', 'B');

        $ladingCode = $prefix . $timer . $getNum . $randInt;

        return $ladingCode;
    }

    public function countStatusByCondititon($payload) {
        $result = array();
        foreach ( $payload['aryStatus'] as $status ) {
            $payload['status'] = $status;
            $result[$status] = $this->_ordersInterface->checkExist($payload);
        }

        return $result;
    }
}
