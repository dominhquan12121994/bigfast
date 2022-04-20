<?php
/**
 * Class OrderTraceRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderTraceInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderTrace;

class OrderTraceRepository extends AbstractEloquentRepository implements OrderTraceInterface
{
    protected function _getModel()
    {
        return OrderTrace::class;
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

        return $query;
    }
}
