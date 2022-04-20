<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Services;

use DB;
use Exception;
use Throwable;
use Illuminate\Support\MessageBag;

use App\Modules\Orders\Models\Entities\Orders;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShipAssignedInterface;

class OrderShipAssignedServices
{

    protected $_shopsInterface;
    protected $_ordersInterface;
    protected $_shopAddressInterface;
    protected $_orderShipAssignedInterface;

    public function __construct(ShopsInterface $shopsInterface,
                                OrdersInterface $ordersInterface,
                                ShopAddressInterface $shopAddressInterface,
                                OrderShipAssignedInterface $orderShipAssignedInterface)
    {
        $this->_shopsInterface = $shopsInterface;
        $this->_ordersInterface = $ordersInterface;
        $this->_shopAddressInterface = $shopAddressInterface;
        $this->_orderShipAssignedInterface = $orderShipAssignedInterface;
    }

    public function getOrderPickupSearch($payload = array()) {
        $result = array();
        $user_id = $payload['assignUserIdSearch'];

        $shops = DB::table('order_ship_assigned')->distinct()
            ->join('orders', 'order_ship_assigned.order_id', '=', 'orders.id')
            ->select('orders.shop_id', 'orders.sender_id')
            ->where('user_id', $user_id)
            ->get()->transform(function ($item) {
                return array($item->shop_id, $item->sender_id);
            })->toArray();

        if (count($shops) > 0) {
            foreach ($shops as $shop) {
                $conditions = array(
                    'assignShopIdSearch' => $shop[0],
                    'assignUserIdSearch' => $user_id,
                    'orderSenderSearch' => $shop[1],
                    'assignStatus' => 1
                );
                $shopInfo = $this->_shopsInterface->getById($shop[0]);
                $shopAddress = $this->_shopAddressInterface->getById($shop[1]);
                $orderByShop = $this->_ordersInterface->getMore($conditions, array(
                    'with' => array('extra', 'products'),
                    'orderBy' => 'status_detail',
                    'direction' => 'ASC',
                ));
                if (count($orderByShop) > 0) {
                    $item = new \stdClass();
                    $item->shop = array(
                        'name' => $shopInfo->name,
                        'sender' => $shopAddress->name,
                        'phone' => $shopAddress->phone,
                        'address' => $shopAddress->address
                    );
                    $item->orders = $orderByShop;
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    public function getOrderPickup($payload = array()) {
        $result = array();
        $user_id = $payload['user_id'];
        $statusActive = $payload['statusActive'];
        $arrStatusDetail = $payload['arrStatusDetail'];

        $shops = DB::table('order_ship_assigned')->distinct()
            ->join('orders', 'order_ship_assigned.order_id', '=', 'orders.id')
            ->select('orders.shop_id', 'orders.sender_id')
            ->where('user_id', $user_id)
            ->where('time_success', 0)
            ->get()->transform(function ($item) {
                return array($item->shop_id, $item->sender_id);
            })->toArray();

        if (count($shops) > 0) {
            foreach ($shops as $shop) {
                $conditions = array(
                    'getOrderPickup_shop_id' => $shop[0],
                    'getOrderPickup_user_id' => $user_id,
                    'getOrderPickup_user_role' => 'pickup',
                    'getOrderPickup_status_detail' => $statusActive,
                    'getOrderPickup_sender_id' => $shop[1]
                );
                $shopInfo = $this->_shopsInterface->getById($shop[0]);
                $shopAddress = $this->_shopAddressInterface->getById($shop[1]);
                $orderByShop = $this->_ordersInterface->getMore($conditions, array(
                    'with' => array('extra', 'products', 'logs'),
                    'orderBy' => array('status_detail', 'updated_at'),
                    'direction' => array('ASC')
                ));
                if (count($orderByShop) > 0) {
                    $logs = array();
                    foreach ($orderByShop as $order) {
                        $logs += $order->logs->filter(function ($item){ return $item->log_type === 'call_history'; })
                            ->transform(function ($item){ return $item->note1; })->toArray();
                    }

                    $item = new \stdClass();
                    $item->shop = array(
                        'name' => $shopInfo->name,
                        'sender' => $shopAddress->name,
                        'phone' => $shopAddress->phone,
                        'address' => $shopAddress->address
                    );
                    $item->orders = $orderByShop;
                    $item->logs = array_values(array_unique(array_values($logs)));
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    public function getOrderPickupDone($payload, &$totalDone) {
        $result = array();
        $user_id = $payload['user_id'];
        $statusActive = $payload['statusActive'];

        $shops = DB::table('order_ship_assigned')->distinct()
            ->join('orders', 'order_ship_assigned.order_id', '=', 'orders.id')
            ->select('orders.shop_id', 'orders.sender_id')
            ->where('user_id', $user_id)
            ->where('order_ship_assigned.status', 1)
            ->where('time_success' , '!=', 0)
            ->get()->transform(function ($item){ return array($item->shop_id, $item->sender_id); })->toArray();

        if (count($shops) > 0) {
            foreach ($shops as $shop) {
                $conditions = array(
                    'assignShopIdDone' => $shop[0],
                    'assignUserRoleDone' => 'pickup',
                    'assignUserIdDone' => $user_id,
                    'orderSenderDone'  => $shop[1],
                    'orderDoneStatus'  => 1
                );
                $shopInfo = $this->_shopsInterface->getById($shop[0]);
                $shopAddress = $this->_shopAddressInterface->getById($shop[1]);
                $orderByShop = $this->_ordersInterface->getMore($conditions, array(
                    'with' => array('extra', 'products', 'logs'),
                    'orderBy' => array('status_detail', 'updated_at'),
                    'direction' => array('ASC')
                ));
                if (count($orderByShop) > 0) {
                    $totalDone += count($orderByShop);

                    if ($statusActive === 22) {
                        $logs = array();
                        foreach ($orderByShop as $order) {
                            $logs += $order->logs->filter(function ($item){ return $item->log_type === 'call_history'; })
                                ->transform(function ($item){ return $item->note1; })->toArray();
                        }

                        $item = new \stdClass();
                        $item->shop = array(
                            'name' => $shopInfo->name,
                            'sender' => $shopAddress->name,
                            'phone' => $shopAddress->phone,
                            'address' => $shopAddress->address
                        );
                        $item->orders = $orderByShop;
                        $item->logs = array_values(array_unique(array_values($logs)));
                        $result[] = $item;
                    }
                }
            }
        }
        return $result;
    }

    public function getOrderRefund($payload = array()) {
        $result = array();
        $user_id = $payload['user_id'];
        $statusActive = $payload['statusActive'];
        $arrStatusDetail = $payload['arrStatusDetail'];

        $shops = DB::table('order_ship_assigned')->distinct()
            ->join('orders', 'order_ship_assigned.order_id', '=', 'orders.id')
            ->select('orders.shop_id', 'orders.refund_id')
            ->where('user_id', $user_id)
            ->where('order_ship_assigned.status', 3)
            ->where('time_success', 0)
            ->get()->transform(function ($item) {
                return array($item->shop_id, $item->refund_id);
            })->toArray();

        if (count($shops) > 0) {
            foreach ($shops as $shop) {
                $conditions = array(
                    'getOrderRefund_shop_id' => $shop[0],
                    'getOrderRefund_user_id' => $user_id,
                    'getOrderRefund_user_role' => 'refund',
                    'getOrderRefund_status_detail' => $statusActive,
                    'getOrderRefund_refund_id' => $shop[1]
                );
                $shopInfo = $this->_shopsInterface->getById($shop[0]);
                $shopAddress = $this->_shopAddressInterface->getById($shop[1]);
                $orderByShop = $this->_ordersInterface->getMore($conditions, array(
                    'with' => array('extra', 'products', 'logs'),
                    'orderBy' => array('status_detail', 'updated_at'),
                    'direction' => array('ASC')
                ));
                if (count($orderByShop) > 0) {
                    $logs = array();
                    foreach ($orderByShop as $order) {
                        $logs += $order->logs->filter(function ($item){ return $item->log_type === 'call_history'; })
                            ->transform(function ($item){ return $item->note1; })->toArray();
                    }

                    $item = new \stdClass();
                    $item->shop = array(
                        'name' => $shopInfo->name,
                        'sender' => $shopAddress->name,
                        'phone' => $shopAddress->phone,
                        'address' => $shopAddress->address
                    );
                    $item->orders = $orderByShop;
                    $item->logs = array_values(array_unique(array_values($logs)));
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    public function getOrderRefundSearch($payload = array()) {
        $result = array();
        $user_id = $payload['assignUserIdSearch'];

        $shops = DB::table('order_ship_assigned')->distinct()
            ->join('orders', 'order_ship_assigned.order_id', '=', 'orders.id')
            ->select('orders.shop_id', 'orders.refund_id')
            ->where('user_id', $user_id)
            ->where('order_ship_assigned.status', 3)
            ->get()->transform(function ($item) {
                return array($item->shop_id, $item->refund_id);
            })->toArray();

        if (count($shops) > 0) {
            foreach ($shops as $shop) {
                $conditions = array(
                    'assignShopIdSearch' => $shop[0],
                    'assignUserIdSearch' => $user_id,
                    'orderRefundSearch' => $shop[1],
                    'assignStatus' => 3
                );
                $shopInfo = $this->_shopsInterface->getById($shop[0]);
                $shopAddress = $this->_shopAddressInterface->getById($shop[1]);
                $orderByShop = $this->_ordersInterface->getMore($conditions, array(
                    'with' => array('extra', 'products', 'logs'),
                    'orderBy' => array('status_detail', 'updated_at'),
                    'direction' => array('ASC')
                ));
                if (count($orderByShop) > 0) {
                    $logs = array();
                    foreach ($orderByShop as $order) {
                        $logs += $order->logs->filter(function ($item){ return $item->log_type === 'call_history'; })
                            ->transform(function ($item){ return $item->note1; })->toArray();
                    }

                    $item = new \stdClass();
                    $item->shop = array(
                        'name' => $shopInfo->name,
                        'sender' => $shopAddress->name,
                        'phone' => $shopAddress->phone,
                        'address' => $shopAddress->address
                    );
                    $item->orders = $orderByShop;
                    $item->logs = array_values(array_unique(array_values($logs)));
                    $result[] = $item;
                }
            }
        }
        return $result;
    }

    public function getOrderRefundDone($payload, &$totalDone) {
        $result = array();
        $user_id = $payload['user_id'];
        $statusActive = $payload['statusActive'];

        $shops = DB::table('order_ship_assigned')->distinct()
            ->join('orders', 'order_ship_assigned.order_id', '=', 'orders.id')
            ->select('orders.shop_id', 'orders.refund_id')
            ->where('user_id', $user_id)
            ->where('time_success' , '!=', 0)
            ->where('order_ship_assigned.status', 3)
            ->get()->transform(function ($item){ return array($item->shop_id, $item->refund_id); })->toArray();

        if (count($shops) > 0) {
            foreach ($shops as $shop) {
                $conditions = array(
                    'assignShopIdDone' => $shop[0],
                    'assignUserRoleDone' => 'refund',
                    'assignUserIdDone' => $user_id,
                    'orderRefundDone'  => $shop[1],
                    'orderDoneStatus' => 3
                );
                $shopInfo = $this->_shopsInterface->getById($shop[0]);
                $shopAddress = $this->_shopAddressInterface->getById($shop[1]);
                $orderByShop = $this->_ordersInterface->getMore($conditions, array(
                    'with' => array('extra', 'products'),
                    'orderBy' => array('status_detail', 'updated_at'),
                    'direction' => array('ASC')
                ));
//                $orderByShop = $this->_orderShipAssignedInterface->getMore($conditions, array(
//                    'with' => array('order.extra', 'order.products', 'order.sender', 'order.sender.provinces', 'order.sender.districts', 'order.sender.wards')
//                ));
                if (count($orderByShop) > 0) {
                    $totalDone += count($orderByShop);
                    if ($statusActive === 82) {
                        $item = new \stdClass();
                        $item->shop = array(
                            'name' => $shopInfo->name,
                            'sender' => $shopAddress->name,
                            'phone' => $shopAddress->phone,
                            'address' => $shopAddress->address
                        );
                        $item->orders = $orderByShop;
                        $result[] = $item;
                    }
                }
            }
        }
        return $result;
    }

    public function countByStatus($payload = array()) {
        $result = array();
        $user_id = $payload['user_id'];
        $user_type = $payload['user_type'];
        $arrStatusDetail = $payload['arrStatusDetail'];

        $user_type_status = array('pickup' => 1, 'shipper' => 2, 'refund' => 3);

        $buildQuery = Orders::groupBy('orders.status_detail')
            ->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id')
            ->select('orders.status_detail', DB::raw('count(distinct(`orders`.`id`)) as total'))
            ->where('order_ship_assigned.user_role', $user_type)
            ->where('order_ship_assigned.user_id', $user_id);

        if (count($arrStatusDetail) > 0) {
            $buildQuery->where(function($bq) use ($arrStatusDetail) {
                foreach ($arrStatusDetail as $status_detail) {
                    $bq->orWhere(function($q) use ($status_detail) {
                        $q->where('orders.status_detail', $status_detail);
                        $q->where('order_ship_assigned.failed_status', $status_detail);
                    });
                }
            });
        }
        $countOrderByStatus = $buildQuery->get();

//        $statusCount = DB::select( 'SELECT
//            o.status_detail, COUNT(o.id) as total
//        FROM
//            orders AS o
//            RIGHT JOIN order_ship_assigned AS a ON a.order_id = o.id
//        WHERE
//            a.user_id = '.$user_id.' AND
//            a.status = '.$user_type_status[$user_type].' AND
//            a.failed_status IN ('.implode(',', $arrStatusDetail).')
//        GROUP BY
//            o.status_detail');

        if (count($countOrderByStatus) > 0) {
            foreach ($countOrderByStatus as $key => $count) {
                $result[$count->status_detail] = $count->total;
            }
        }
        return $result;
    }

    public function getStaffInfo(&$staffsInfo, $ordersList, $type) {
        $aryOrder = $ordersList->pluck('id')->all();
        $conditions['order_id'] = $aryOrder;
        $conditions['user_role'] = $type;
        $staffsInfo = $this->_orderShipAssignedInterface->getMore($conditions, array(
            'with' => array('user'),
            'orderBy' => 'id'
        ))->groupBy('order_id')->transform(function ($item) {
            return $item->first()->user->name . '<br>' . $item->first()->user->phone;
        });

        return;
    }
}
