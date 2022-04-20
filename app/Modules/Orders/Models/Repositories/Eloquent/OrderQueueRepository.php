<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class OrderQueueRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderQueueInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderQueue;

class OrderQueueRepository extends AbstractEloquentRepository implements OrderQueueInterface
{
    protected function _getModel()
    {
        return OrderQueue::class;
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

        if (isset($conditions['status'])) {
            $status = $conditions['status'];
            $query->whereIn('status', array(0, 2));
        }

        if (isset($conditions['created_range'])) {
            $created_date = $conditions['created_range'];
            if (is_array($created_date)) {
                $created_date[0] = (int)$created_date[0];
                $created_date[1] = (int)$created_date[1];
                $query->whereBetween('created_date', $created_date);
            } else {
                $query->where('created_date', $created_date);
            }
        }

        return $query;
    }
}
