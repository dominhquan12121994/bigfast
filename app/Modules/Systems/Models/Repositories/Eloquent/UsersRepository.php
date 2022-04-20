<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Models\Repositories\Eloquent;

use App\Modules\Systems\Models\Entities\User;
use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Systems\Models\Repositories\Contracts\UsersInterface;

/**
 * Class UsersRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author HuyDien <huydien.it@gmail.com>
 */
class UsersRepository extends AbstractEloquentRepository implements UsersInterface
{
    /**
     * @return mixed|string
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _getModel()
    {
        return User::class;
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
            $query->orWhereLike('name', $name);
        }

        if (isset($conditions['email'])) {
            $email = $conditions['email'];
            $query->orWhereLike('email', $email);
        }

        return $query;
    }
}
