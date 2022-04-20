<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class OrderShipAssignedRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use Illuminate\Support\Facades\DB;
use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShipAssignedInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderShipAssigned;

class OrderShipAssignedRepository extends AbstractEloquentRepository implements OrderShipAssignedInterface
{
    protected function _getModel()
    {
        return OrderShipAssigned::class;
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

        if (isset($conditions['shop_id'])) {
            $shop_id = $conditions['shop_id'];
            $query->where('shop_id', $shop_id);
        }

        if (isset($conditions['user_role'])) {
            $user_role = $conditions['user_role'];
            $query->where('user_role', $user_role);
        }

        if (isset($conditions['user_id'])) {
            $user_id = $conditions['user_id'];
            $query->where('user_id', $user_id);
        }

        if (isset($conditions['order_id'])) {
            $order_id = $conditions['order_id'];
            if(is_array($order_id)){
                $query->whereIn('order_id', $order_id);
            }else {
                $query->where('order_id', $order_id);
            }
        }

        if (isset($conditions['status'])) {
            $status = $conditions['status'];
            $query->where('status', $status);
        }

        if (isset($conditions['time_assigned'])) {
            $time_assigned = $conditions['time_assigned'];
            $query->where('time_assigned', $time_assigned);
        }

        if (isset($conditions['time_success'])) {
            $time_success = $conditions['time_success'];
            if ($time_success === true) {
                $query->where('time_success', '!=', 0);
            } else {
                $query->where('time_success', $time_success);
            }
        }

        if (isset($conditions['time_failed'])) {
            $time_failed = $conditions['time_failed'];
            if ($time_failed === true) {
                $query->where('time_failed', '!=', 0);
            } else {
                $query->where('time_failed', $time_failed);
            }
        }

        if (isset($conditions['failed_status'])) {
            $failed_status = $conditions['failed_status'];
            $query->where('failed_status', $failed_status);
        }

        if (isset($conditions['status_detail'])) {
            $status_detail = $conditions['status_detail'];
            $query->where('status_detail', $status_detail);
        }

        if (isset($conditions['onlyTrashed'])) {
            $query->onlyTrashed();
        }

        if (isset($conditions['withTrashed'])) {
            $query->withTrashed();
        }

        if (isset($conditions['deleted_at'])) {
            $deleted_at = $conditions['deleted_at'];
            $query->whereDate('deleted_at', $deleted_at);
        }

        return $query;
    }
}
