<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class OrderShopTransferRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShopTransferInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderShopTransfer;

class OrderShopTransferRepository extends AbstractEloquentRepository implements OrderShopTransferInterface
{
    protected function _getModel()
    {
        return OrderShopTransfer::class;
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

        return $query;
    }
}
