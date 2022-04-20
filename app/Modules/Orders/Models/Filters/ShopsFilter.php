<?php
/**
 * Class ShopsFilter
 * @package App\Modules\Orders\Models\Filters
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Filters;

//use App\Models\Filters\AbstractFilter;

trait ShopsFilter
{
    public function scopeOrWhereLike($query, $column, $value)
    {
        return $query->orWhere($column, 'like', '%'.$value.'%');
    }
}
