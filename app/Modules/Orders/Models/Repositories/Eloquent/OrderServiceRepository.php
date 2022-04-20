<?php
/**
 * Class OrderExtraRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderService;

class OrderServiceRepository extends AbstractEloquentRepository implements OrderServiceInterface
{
    protected function _getModel()
    {
        return OrderService::class;
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

        if (isset($conditions['status']) ) {
            $query->where('status', $conditions['status']);
        }

        if (isset($conditions['alias']) ) {
            $query->where('alias', $conditions['alias']);
        }

        return $query;
    }
}
