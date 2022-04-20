<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Modules\Orders\Models\Entities\OrderShop;
use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Entities\ShopReconcileHistory;
use App\Modules\Orders\Models\Repositories\Contracts\ShopReconcileInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;

/**
 * Class ShopsRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author HuyDien <huydien.it@gmail.com>
 */
class ShopReconcileRepository extends AbstractEloquentRepository implements ShopReconcileInterface
{
    /**
     * @return mixed|string
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _getModel()
    {
        return ShopReconcileHistory::class;
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

        if (isset($conditions['date_range'])) {
            $date_range = $conditions['date_range'];
            $query->whereBetween('end_date', $date_range);
        }

        return $query;
    }
}
