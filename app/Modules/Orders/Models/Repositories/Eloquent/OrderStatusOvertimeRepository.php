<?php
/**
 * Class OrderStatusOvertimeRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderStatusOvertimeInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderStatusOvertime;

class OrderStatusOvertimeRepository extends AbstractEloquentRepository implements OrderStatusOvertimeInterface
{
    protected function _getModel()
    {
        return OrderStatusOvertime::class;
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

        if (isset($conditions['order_id'])) {
            $order_id = $conditions['order_id'];
            if (is_array($order_id)) {
                $query->whereIn('order_id', $order_id);
            } else {
                $query->where('order_id', $order_id);
            }
        }

        if (isset($conditions['shop_id'])) {
            $shop_id = (int)$conditions['shop_id'];
            $query->where('shop_id', $shop_id);
        }

        if (isset($conditions['status_detail'])) {
            $status_detail = (int)$conditions['status_detail'];
            $query->where('status_detail', $status_detail);
        }

        if (isset($conditions['end_date'])) {
            $end_date = (int)$conditions['end_date'];
            $query->where('end_date', $end_date);
        }

        if (isset($conditions['end_date_null'])) {
            $query->whereNull('end_date');
        }

        if (isset($conditions['start_date'])) {
            $range = $conditions['start_date'];
            $query->whereBetween('start_date', $range);
        }

        if (isset($conditions['start_date_less_than'])) {
            $start_date_less_than = $conditions['start_date_less_than'];
            $query->where('start_date','<=' , $start_date_less_than);
        }
        
        return $query;
    }
}
