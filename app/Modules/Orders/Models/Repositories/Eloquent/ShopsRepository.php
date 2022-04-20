<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Modules\Orders\Models\Entities\OrderShop;
use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;

/**
 * Class ShopsRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author HuyDien <huydien.it@gmail.com>
 */
class ShopsRepository extends AbstractEloquentRepository implements ShopsInterface
{
    /**
     * @return mixed|string
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _getModel()
    {
        return OrderShop::class;
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

        if (isset($conditions['phone'])) {
            $phone = $conditions['phone'];
            $query->orWhereLike('phone', $phone);
        }

        if (isset($conditions['name'])) {
            $name = $conditions['name'];
            $query->orWhereLike('name', $name);
        }

        if (isset($conditions['email'])) {
            $email = $conditions['email'];
            $query->orWhereLike('email', $email);
        }

        return $query;
    }
}
