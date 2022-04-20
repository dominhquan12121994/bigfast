<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class OrderShopReconcileRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShopReconcileInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderShopReconcile;

class OrderShopReconcileRepository extends AbstractEloquentRepository implements OrderShopReconcileInterface
{
    protected function _getModel()
    {
        return OrderShopReconcile::class;
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

        if (isset($conditions['date'])) {
            $date = $conditions['date'];
            $query->where('date', $date);
        }

        if (isset($conditions['date_range'])) {
            $date_range = $conditions['date_range'];
            $query->whereBetween('date', $date_range);
        }

        return $query;
    }
}
