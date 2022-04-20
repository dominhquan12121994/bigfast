<?php

/**
 * Class Orders
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;

/* Model */
use App\Modules\Orders\Models\Entities\Orders;

class OrdersRepository extends AbstractEloquentRepository implements OrdersInterface
{
    protected function _getModel()
    {
        return Orders::class;
    }

    /**
     * @param $conditions
     * @param $query
     * @return mixed
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _prepareConditions($conditions, $query)
    {
        $query = parent::_prepareConditions($conditions, $query);

        if (isset($conditions['assignedShipper'])) {
            $user_id = $conditions['assignedShipper'];
            $user_role = $conditions['assignedRole'];
            $failed_status = $conditions['assignedFailedStatus'];

            $query->select('orders.*');
            $query->distinct();
            $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
            $query->where('order_ship_assigned.user_role', $user_role);
            $query->where('order_ship_assigned.user_id', $user_id);
            $query->where('order_ship_assigned.failed_status', $failed_status);
        }

        if (isset($conditions['getByShipper_user_id'])) {
            $user_id = $conditions['getByShipper_user_id'];
            $user_role = $conditions['getByShipper_user_role'];
            $status_detail = $conditions['getByShipper_status_detail'];

            if ($status_detail === 22) {
                $query->select('orders.*');
                $query->distinct();
                $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
                $query->where('order_ship_assigned.user_role', $user_role);
                $query->where('order_ship_assigned.user_id', $user_id);
                $query->where('order_ship_assigned.time_success', '!=', 0);
                $query->where('order_ship_assigned.status', 1);
            } elseif ($status_detail === 51) {
                $query->select('orders.*');
                $query->distinct();
                $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
                $query->where('order_ship_assigned.user_role', $user_role);
                $query->where('order_ship_assigned.user_id', $user_id);
                $query->where('order_ship_assigned.time_success', '!=', 0);
                $query->where('order_ship_assigned.status', 2);
            } elseif ($status_detail === 82) {
                $query->select('orders.*');
                $query->distinct();
                $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
                $query->where('order_ship_assigned.user_role', $user_role);
                $query->where('order_ship_assigned.user_id', $user_id);
                $query->where('order_ship_assigned.time_success', '!=', 0);
                $query->where('order_ship_assigned.status', 3);
            } elseif ($status_detail === 71) {
                $query->select('orders.*');
                $query->distinct();
                $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
                $query->whereIn('order_ship_assigned.user_role', array('shipper', 'refund'));
                $query->where('order_ship_assigned.user_id', $user_id);
                $query->where('orders.status_detail', $status_detail);
                $query->where('order_ship_assigned.failed_status', $status_detail);
            } else {
                $query->select('orders.*');
                $query->distinct();
                $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
                $query->where('order_ship_assigned.user_role', $user_role);
                $query->where('order_ship_assigned.user_id', $user_id);

                if (is_array($status_detail)) {
                    if (!in_array(61, $status_detail)) {
                        $query->whereIn('orders.status_detail', $status_detail);
                        $query->whereIn('order_ship_assigned.failed_status', $status_detail);
                    } else {
                        $query->withTrashed();
                    }
                } else {
                    $query->where('orders.status_detail', $status_detail);
                    $query->where('order_ship_assigned.failed_status', $status_detail);

                    if ($status_detail === 61) $query->withTrashed();
                }
            }
        }

        if (isset($conditions['getOrderPickup_user_id'])) {
            $user_id = $conditions['getOrderPickup_user_id'];
            $user_role = $conditions['getOrderPickup_user_role'];
            $status_detail = $conditions['getOrderPickup_status_detail'];

            $query->select('orders.*');
            $query->distinct();
            $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
            $query->where('order_ship_assigned.user_role', $user_role);
            $query->where('order_ship_assigned.user_id', $user_id);

            if (isset($conditions['getOrderPickup_shop_id'])) {
                $shop_id = $conditions['getOrderPickup_shop_id'];
                $query->where('orders.shop_id', $shop_id);
            }
            if (isset($conditions['getOrderPickup_sender_id'])) {
                $orderSender = $conditions['getOrderPickup_sender_id'];
                $query->where('orders.sender_id', $orderSender);
            }

            if (is_array($status_detail)) {
                if (!in_array(61, $status_detail)) {
                    $query->whereIn('orders.status_detail', $status_detail);
                    $query->whereIn('order_ship_assigned.failed_status', $status_detail);
                } else {
                    $query->withTrashed();
                }
            } else {
                $query->where('orders.status_detail', $status_detail);
                $query->where('order_ship_assigned.failed_status', $status_detail);

                if ($status_detail === 61) $query->withTrashed();
            }
        }

        if (isset($conditions['getOrderRefund_user_id'])) {
            $user_id = $conditions['getOrderRefund_user_id'];
            $user_role = $conditions['getOrderRefund_user_role'];
            $status_detail = $conditions['getOrderRefund_status_detail'];

            $query->select('orders.*');
            $query->distinct();
            $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
            $query->where('order_ship_assigned.user_role', $user_role);
            $query->where('order_ship_assigned.user_id', $user_id);

            if (isset($conditions['getOrderRefund_shop_id'])) {
                $shop_id = $conditions['getOrderRefund_shop_id'];
                $query->where('orders.shop_id', $shop_id);
            }
            if (isset($conditions['getOrderRefund_refund_id'])) {
                $orderRefund = $conditions['getOrderRefund_refund_id'];
                $query->where('orders.refund_id', $orderRefund);
            }

            if (is_array($status_detail)) {
                if (!in_array(61, $status_detail)) {
                    $query->whereIn('orders.status_detail', $status_detail);
                    $query->whereIn('order_ship_assigned.failed_status', $status_detail);
                } else {
                    $query->withTrashed();
                }
            } else {
                $query->where('orders.status_detail', $status_detail);
                $query->where('order_ship_assigned.failed_status', $status_detail);

                if ($status_detail === 61) $query->withTrashed();
            }
        }

        if (isset($conditions['assignUserId'])) {
            $user_id = $conditions['assignUserId'];
            $user_role = $conditions['assignUserRole'];
            $status = (int)$conditions['assignStatus'];

            $query->select('orders.*');
            $query->distinct();
            $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
            $query->where('order_ship_assigned.user_role', $user_role);
            $query->where('order_ship_assigned.user_id', $user_id);
            if ($status) {
                if ($status !== 61)
                    $query->where('order_ship_assigned.failed_status', $status);
                if ($status === 13) {
                    $query->where('order_ship_assigned.time_failed', '!=', 0);
                }
                if ($status === 22) {
                    $query->where('order_ship_assigned.time_success', '!=', 0);
                }
                if ($status === 23) {
                    $query->where('order_ship_assigned.time_success', '=', 0);
                }
                if ($status === 24) {
                    $query->where('order_ship_assigned.time_failed', '!=', 0);
                }
                if ($status === 33) {
                    $query->where('order_ship_assigned.time_failed', '!=', 0);
                }
                if ($status === 51) {
                    $query->where('order_ship_assigned.time_success', '!=', 0);
                }
                if ($status === 52) {
                    $query->where('order_ship_assigned.time_success', '!=', 0);
                }
                if ($status === 41) {
                    $query->where('order_ship_assigned.time_failed', '!=', 0);
                }
                if ($status === 32) {
                    $query->where('order_ship_assigned.time_success', '=', 0);
                    $query->where('order_ship_assigned.time_failed', '=', 0);
                }
                if ($status === 34) {
                    $query->where('order_ship_assigned.time_failed', '!=', 0);
                }
                if ($status === 71) {
                    $query->where('order_ship_assigned.time_failed', '!=', 0);
                }
                if ($status === 72) {
                    $query->where('order_ship_assigned.time_failed', '!=', 0);
                }
                if ($status === 82) {
                    $query->where('order_ship_assigned.time_success', '!=', 0);
                }
            } else {
                if (isset($conditions['status_detail_shipper'])) {
                    if (count($conditions['status_detail_shipper']) > 0) {
                        $query->where(function($bq) use ($conditions) {
                            foreach ($conditions['status_detail_shipper'] as $status) {
                                $bq->orWhere(function($q) use ($status) {
                                    $status = (int)$status;
                                    $q->where('orders.status_detail', $status);
                                    if ($status === 13) {
                                        $q->where('order_ship_assigned.time_failed', '!=', 0);
                                    }
                                    if ($status === 22) {
                                        $q->where('order_ship_assigned.time_success', '!=', 0);
                                    }
                                    if ($status === 23) {
                                        $q->where('order_ship_assigned.time_success', '=', 0);
                                    }
                                    if ($status === 24) {
                                        $q->where('order_ship_assigned.time_failed', '!=', 0);
                                    }
                                    if ($status === 33) {
                                        $q->where('order_ship_assigned.time_failed', '!=', 0);
                                    }
                                    if ($status === 51) {
                                        $q->where('order_ship_assigned.time_success', '!=', 0);
                                    }
                                    if ($status === 52) {
                                        $q->where('order_ship_assigned.time_success', '!=', 0);
                                    }
                                    if ($status === 41) {
                                        $q->where('order_ship_assigned.time_failed', '!=', 0);
                                    }
                                    if ($status === 32) {
                                        $q->where('order_ship_assigned.time_success', '=', 0);
                                        $q->where('order_ship_assigned.time_failed', '=', 0);
                                    }
                                    if ($status === 34) {
                                        $q->where('order_ship_assigned.time_failed', '!=', 0);
                                    }
                                    if ($status === 71) {
                                        $q->where('order_ship_assigned.time_failed', '!=', 0);
                                    }
                                    if ($status === 72) {
                                        $q->where('order_ship_assigned.time_failed', '!=', 0);
                                    }
                                    if ($status === 82) {
                                        $q->where('order_ship_assigned.time_success', '!=', 0);
                                    }
                                });
                            }
                        });
                    }
                }
            }

            if (isset($conditions['assignShopId'])) {
                $shop_id = $conditions['assignShopId'];
                $query->where('orders.shop_id', $shop_id);
            }
            if (isset($conditions['orderSender'])) {
                $orderSender = $conditions['orderSender'];
                $query->where('orders.sender_id', $orderSender);
            }
            if (isset($conditions['orderRefund'])) {
                $orderRefund = $conditions['orderRefund'];
                $query->where('orders.refund_id', $orderRefund);
            }
            if ($status === 61) $query->withTrashed();
        }

        if (isset($conditions['assignUserIdDone'])) {
            $user_id = $conditions['assignUserIdDone'];
            $user_role = $conditions['assignUserRoleDone'];
            $query->select('orders.*');
            $query->distinct();
            $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
            $query->where('order_ship_assigned.user_role', $user_role);
            $query->where('order_ship_assigned.user_id', $user_id);
            $query->where('order_ship_assigned.time_success', '!=', 0);
            if (isset($conditions['orderDoneStatus'])) {
                $status = $conditions['orderDoneStatus'];
                $query->where('order_ship_assigned.status', $status);
            }
            if (isset($conditions['assignShopIdDone'])) {
                $shop_id = $conditions['assignShopIdDone'];
                $query->where('orders.shop_id', $shop_id);
            }
            if (isset($conditions['orderSenderDone'])) {
                $orderSender = $conditions['orderSenderDone'];
                $query->where('orders.sender_id', $orderSender);
            }
            if (isset($conditions['orderRefundDone'])) {
                $orderRefund = $conditions['orderRefundDone'];
                $query->where('orders.refund_id', $orderRefund);
            }
        }

        if (isset($conditions['assignUserIdShip'])) {
//            $user_id = $conditions['assignUserIdShip'];
//            $status = $conditions['assignStatusShip'];
//
//            $query->select('orders.*');
//            $query->distinct();
//            $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
//            $query->where('order_ship_assigned.failed_status', $status);
//            $query->where('order_ship_assigned.user_id', $user_id);
//            $query->where('order_ship_assigned.time_success', 0);
//            if (isset($conditions['assignShopIdShip'])) {
//                $shop_id = $conditions['assignShopIdShip'];
//                $query->where('orders.shop_id', $shop_id);
//            }

            $user_id = $conditions['assignUserIdShip'];
            $user_role = $conditions['assignUserRoleShip'];
            $status_detail = $conditions['assignStatusShip'];

            $query->select('orders.*');
            $query->distinct();
            $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
            $query->where('order_ship_assigned.user_role', $user_role);
            $query->where('order_ship_assigned.user_id', $user_id);

            if (is_array($status_detail)) {
                if (!in_array(61, $status_detail)) {
                    $query->whereIn('orders.status_detail', $status_detail);
                    $query->whereIn('order_ship_assigned.failed_status', $status_detail);
                } else {
                    $query->withTrashed();
                }
            } else {
                $query->where('orders.status_detail', $status_detail);
                $query->where('order_ship_assigned.failed_status', $status_detail);

                if ($status_detail === 61) $query->withTrashed();
            }
        }

        if (isset($conditions['assignUserIdShipDone'])) {
            $user_id = $conditions['assignUserIdShipDone'];
            $user_role = $conditions['assignUserRoleShipDone'];

            $query->select('orders.*');
            $query->distinct();
            $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
            $query->where('order_ship_assigned.user_role', $user_role);
            $query->where('order_ship_assigned.user_id', $user_id);
            $query->where('order_ship_assigned.time_success', '!=', 0);
        }

        if (isset($conditions['assignUserIdSearch'])) {
            $user_id = $conditions['assignUserIdSearch'];
            $assignStatus = $conditions['assignStatus'];

            $query->select('orders.*');
            $query->distinct();
            $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
            $query->where('order_ship_assigned.user_id', $user_id);
            $query->where('order_ship_assigned.status', $assignStatus);
        }

        if (isset($conditions['assignUserCallHistory'])) {
            $user_id = $conditions['assignUserCallHistory'];
            $timeAssignRange = $conditions['timeAssignRange'];

            $query->select('orders.*');
            $query->distinct();
            $query->leftjoin('order_ship_assigned','order_ship_assigned.order_id', '=', 'orders.id');
            $query->where('order_ship_assigned.user_id', $user_id);
            $query->whereBetween('order_ship_assigned.time_assigned', $timeAssignRange);
        }

        if (isset($conditions['shop_id'])) {
            $shop_id = (int)$conditions['shop_id'];
            $query->where('orders.shop_id', $shop_id);
        }

        if (isset($conditions['status_in'])) {
            $status_in = $conditions['status_in'];
            $query->whereIn('orders.status', $status_in);
        }

        if (isset($conditions['status'])) {
            $status = $conditions['status'];
            if (isset($conditions['join'])) {
                $query->where('orders.status', $status);
            } else {
                if (is_array($status)) {
                    $query->whereIn('orders.status', $status);
                } else {
                    $query->where('orders.status', $status);
                }
            }
        }

        if (isset($conditions['status_detail'])) {
            if ($conditions['status_detail']) {
                $status = $conditions['status_detail'];
                if (isset($conditions['join'])) {
                    $query->where('orders.status_detail', $status);
                } else {
                    if (is_array($status)) {
                        $query->whereIn('orders.status_detail', $status);
                    } else {
                        $query->where('orders.status_detail', $status);
                    }
                }
            }
        }

        if (isset($conditions['lading_code'])) {
            $lading_code = $conditions['lading_code'];
            if (is_array($lading_code)) {
                $query->whereIn('lading_code', $lading_code);
            } elseif (strlen($lading_code) === 12) {
                $query->where('lading_code', $lading_code);
            } else {
                $query->where('lading_code', 'LIKE', '%' . $lading_code);
            }
        }

        if (isset($conditions['created_date'])) {
            $created_date = (int)$conditions['created_date'];
            $query->where('created_date', $created_date);
        }

        if (isset($conditions['created_range'])) {
            $range = $conditions['created_range'];
            $range[0] = (int)$range[0];
            $range[1] = (int)$range[1];
            $query->whereBetween('created_date', $range);
        }

        if (isset($conditions['send_success_range'])) {
            $range = $conditions['send_success_range'];
            $range[0] = (int)$range[0];
            $range[1] = (int)$range[1];
            $query->whereBetween('send_success_date', $range);
        }

        if (isset($conditions['collect_money_range'])) {
            $range = $conditions['collect_money_range'];
            $range[0] = (int)$range[0];
            $range[1] = (int)$range[1];
            $query->whereBetween('collect_money_date', $range);
        }

        if (isset($conditions['reconcile_send_range'])) {
            $range = $conditions['reconcile_send_range'];
            $range[0] = (int)$range[0];
            $range[1] = (int)$range[1];
            $query->whereBetween('reconcile_send_date', $range);
        }

        if (isset($conditions['reconcile_refund_range'])) {
            $range = $conditions['reconcile_refund_range'];
            $range[0] = (int)$range[0];
            $range[1] = (int)$range[1];
            $query->whereBetween('reconcile_refund_date', $range);
        }

        if (isset($conditions['pick_province_id'])) {
            $province_id = $conditions['pick_province_id'];
            $query->where('sender.p_id', $province_id);
        }

        return $query;
    }

    protected function _prepareFetchOptions($fetchOptions, $query){
        $joins = $query->getQuery()->joins;
        if(isset($fetchOptions['orderBy'])){
            if (is_array($fetchOptions['orderBy'])) {
                foreach ($fetchOptions['orderBy'] as $key => $option) {
                    $direction = isset($fetchOptions['direction'][$key]) ? $fetchOptions['direction'][$key] : 'DESC';
                    if (!is_array($direction)) {
                        if ($joins) {
                            $query->orderBy('orders.' . $option, $direction);
                        } else {
                            $query->orderBy($option, $direction);
                        }
                    }
                }
            } else {
                $direction = isset($fetchOptions['direction']) ? $fetchOptions['direction'] : 'DESC';
                if (!is_array($direction)) {
                    if ($joins) {
                        $query->orderBy('orders.' . $fetchOptions['orderBy'], $direction);
                    } else {
                        $query->orderBy($fetchOptions['orderBy'], $direction);
                    }
                }
            }
        }
        if(isset($fetchOptions['skip']) && $fetchOptions['skip']){
            $skip = (int)$fetchOptions['skip'];
        }

        if(isset($fetchOptions['groupBy'])){
            $query->groupBy($fetchOptions['groupBy']);
        }

        return $query;
    }
}
