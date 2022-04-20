<?php
/**
 * Class OrderProductRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderProductInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderProduct;

class OrderProductRepository extends AbstractEloquentRepository implements OrderProductInterface
{
    protected function _getModel()
    {
        return OrderProduct::class;
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

        if (isset($conditions['order_id'])) {
            $order_id = $conditions['order_id'];
            $query->where('order_id', $order_id);
        }

        return $query;
    }
}
