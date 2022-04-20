<?php
/**
 * Class OrderExtraRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderExtraInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderExtra;

class OrderExtraRepository extends AbstractEloquentRepository implements OrderExtraInterface
{
    protected function _getModel()
    {
        return OrderExtra::class;
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

        if (isset($conditions['client_code'])) {
            $client_code = $conditions['client_code'];
            $query->where('client_code', $client_code);
        }

        if (isset($conditions['id_min'])) {
            $id_min = (int)$conditions['id_min'];
            $query->where('id', '>=', $id_min);
        }

        return $query;
    }
}
