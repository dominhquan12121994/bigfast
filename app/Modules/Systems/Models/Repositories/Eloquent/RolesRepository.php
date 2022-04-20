<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Models\Repositories\Eloquent;

use App\Modules\Systems\Models\Entities\Role;
use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Systems\Models\Repositories\Contracts\RolesInterface;

/**
 * Class RolesRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author HuyDien <huydien.it@gmail.com>
 */
class RolesRepository extends AbstractEloquentRepository implements RolesInterface
{
    /**
     * @return mixed|string
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _getModel()
    {
        return Role::class;
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

        if (isset($conditions['name'])) {
            $name = $conditions['name'];
            $query->where('name', $name);
        }

        if (isset($conditions['guard_name'])) {
            $guard_name = $conditions['guard_name'];
            $query->where('guard_name', $guard_name);
        }

        return $query;
    }
}
