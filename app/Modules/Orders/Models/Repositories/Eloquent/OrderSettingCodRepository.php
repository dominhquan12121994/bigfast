<?php
/**
 * Class OrderExtraRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingCodInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderSettingCod;

class OrderSettingCodRepository extends AbstractEloquentRepository implements OrderSettingCodInterface
{
    protected function _getModel()
    {
        return OrderSettingCod::class;
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

        if (isset($conditions['cod'])) {
            $cod = $conditions['cod'];
            $query->where('min', '<=', $cod);
            $query->where('max', '>=', $cod);
        }
        
        return $query;
    }
}
