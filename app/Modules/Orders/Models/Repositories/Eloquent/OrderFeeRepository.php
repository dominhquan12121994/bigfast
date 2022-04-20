<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class OrderFeeRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderFee;

class OrderFeeRepository extends AbstractEloquentRepository implements OrderFeeInterface
{
    protected function _getModel()
    {
        return OrderFee::class;
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

        if (isset($conditions['order_id'])) {
            $order_id = $conditions['order_id'];
            if(is_array($order_id)){
                $query->whereIn('order_id', $order_id);
            }else {
                $query->where('order_id', $order_id);
            }
        }

        if (isset($conditions['date'])) {
            $date = $conditions['date'];
            if (is_array($date)) {
                $query->whereBetween('date', $date);
            } else {
                $query->where('date', $date);
            }
        }

        if (isset($conditions['fee_type'])) {
            $feeType = $conditions['fee_type'];
            if (is_array($feeType)) {
                $query->whereIn('fee_type', $feeType);
            } else {
                $query->where('fee_type', $feeType);
            }
        }

        return $query;
    }
}
