<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Repositories\Eloquent;

use App\Modules\Operators\Models\Entities\PostOffice;
use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Operators\Models\Repositories\Contracts\PostOfficesInterface;

/**
 * Class PostOfficesRepository
 * @package App\Modules\Operators\Models\Repositories\Eloquent
 * @author HuyDien <huydien.it@gmail.com>
 */
class PostOfficesRepository extends AbstractEloquentRepository implements PostOfficesInterface
{
    /**
     * @return mixed|string
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _getModel()
    {
        return PostOffice::class;
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

        if (isset($conditions['p_id'])) {
            $p_id = (int)$conditions['p_id'];
            if ($p_id > 0) $query->where('p_id', $p_id);
        }

        if (isset($conditions['d_id'])) {
            $d_id = (int)$conditions['d_id'];
            if ($d_id > 0)$query->where('d_id', $d_id);
        }

        if (isset($conditions['w_id'])) {
            $w_id = $conditions['w_id'];
            $query->where('w_id', $w_id);
        }

        if (isset($conditions['name'])) {
            $name = normalizer_normalize($conditions['name']);
            $query->where('name', 'like', '%'.$name.'%');
        }

        return $query;
    }
}
