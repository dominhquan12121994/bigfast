<?php
/**
 * Class OrderReceiverRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderReceiverInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderReceiver;

class OrderReceiverRepository extends AbstractEloquentRepository implements OrderReceiverInterface
{
    protected function _getModel()
    {
        return OrderReceiver::class;
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

        if (isset($conditions['phone'])) {
            $phone = $conditions['phone'];
            $query->where('phone', $phone);
        }

        return $query;
    }
}
