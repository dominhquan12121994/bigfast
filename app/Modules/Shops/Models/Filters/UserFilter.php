<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class ShopsFilter
 * @package App\Modules\Orders\Models\Filters
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Shops\Models\Filters;

//use App\Models\Filters\AbstractFilter;

trait UserFilter
{
    public function scopeOrWhereLike($query, $column, $value)
    {
        return $query->orWhere($column, 'like', '%'.$value.'%');
    }
}
