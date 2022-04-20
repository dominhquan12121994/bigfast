<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Systems\Models\Entities\Notification;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationInterface;

/**
 * Class ShopBankRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author HuyDien <huydien.it@gmail.com>
 */
class NotificationRepository extends AbstractEloquentRepository implements NotificationInterface
{
    /**
     * @return mixed|string
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _getModel()
    {
        return Notification::class;
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

        if (isset($conditions['date_range'])) {
            $date_range = $conditions['date_range'];
            $query->whereBetween('created_date', $date_range);
        }

        return $query;
    }
}
