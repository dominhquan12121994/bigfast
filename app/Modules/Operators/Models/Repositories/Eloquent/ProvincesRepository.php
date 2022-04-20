<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Repositories\Eloquent;

use App\Modules\Operators\Models\Entities\ZoneProvinces;
use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;

/**
 * Class ProvinceRepository
 * @package App\Modules\Operators\Models\Repositories\Eloquent
 * @author HuyDien <huydien.it@gmail.com>
 */
class ProvincesRepository extends AbstractEloquentRepository implements ProvincesInterface
{
    /**
     * @return mixed|string
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _getModel()
    {
        return ZoneProvinces::class;
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

        if (isset($conditions['code'])) {
            $code = $conditions['code'];
            $query->where('code', $code);
        }

        if (isset($conditions['alias'])) {
            $alias = $conditions['alias'];
            $query->where('alias', $alias);
        }

        if (isset($conditions['name'])) {
            $name = $conditions['name'];
            $query->where('name', $name);
        }

        return $query;
    }
}
