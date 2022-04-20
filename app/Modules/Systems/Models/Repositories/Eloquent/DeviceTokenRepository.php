<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Models\Repositories\Eloquent;

use App\Modules\Systems\Models\Entities\DeviceToken;
use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Systems\Models\Repositories\Contracts\DeviceTokenInterface;

/**
 * Class DeviceTokenRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author HuyDien <huydien.it@gmail.com>
 */
class DeviceTokenRepository extends AbstractEloquentRepository implements DeviceTokenInterface
{
    /**
     * @return mixed|string
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _getModel()
    {
        return DeviceToken::class;
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

        if (isset($conditions['user_type'])) {
            $user_type = $conditions['user_type'];
            $query->where('user_type', $user_type);
        }

        if (isset($conditions['device_type'])) {
            $device_type = $conditions['device_type'];
            $query->where('device_type', $device_type);
        }

        if (isset($conditions['user_id'])) {
            $user_id = $conditions['user_id'];
            $query->where('user_id', $user_id);
        }

        return $query;
    }
}
