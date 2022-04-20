<?php

/**
 * Class OrdersFilter
 * @package App\Modules\Orders\Models\Filters
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Filters;

use App\Models\Filters\AbstractFilter;

class OrdersFilter extends AbstractFilter
{
    /**
     * Demo filter
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     * @author Electric <huydien.it@gmail.com>
     */
    //public function order($direction = 'desc'){
    //    return $this->builder->orderBy('order', $direction);
    //}
}
