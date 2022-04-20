<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Modules\Orders\Models\Entities\OrderShopBank;
use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;

/**
 * Class ShopBankRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author HuyDien <huydien.it@gmail.com>
 */
class ShopBankRepository extends AbstractEloquentRepository implements ShopBankInterface
{
    /**
     * @return mixed|string
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _getModel()
    {
        return OrderShopBank::class;
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

        if (isset($conditions['cycle_cod'])) {
            $cycle_cod = $conditions['cycle_cod'];
            $query->whereIn('cycle_cod', $cycle_cod);
        }

        if (isset($conditions['date_reconcile'])) {
            $date_reconcile = $conditions['date_reconcile'];
            $query->whereRaw('date_reconcile < ?', array($date_reconcile));
//            $query->whereRaw('DATE_ADD(date_reconcile, INTERVAL cycle_cod_day DAY) < ?', array($date_reconcile));
        }

        return $query;
    }
}
