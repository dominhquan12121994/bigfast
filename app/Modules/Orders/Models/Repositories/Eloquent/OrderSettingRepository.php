<?php
/**
 * Class OrderExtraRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderSetting;

class OrderSettingRepository extends AbstractEloquentRepository implements OrderSettingInterface
{
    protected function _getModel()
    {
        return OrderSetting::class;
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

        if ( isset($conditions['route']) ) {
            $query->where('route', $conditions['route']);
        }

        if ( isset($conditions['service']) ) {
            $query->where('service', $conditions['service']);
        }

        if ( isset($conditions['disable']) ) {
            $query->where('disable', $conditions['disable']);
        }
        
        return $query;
    }
}
