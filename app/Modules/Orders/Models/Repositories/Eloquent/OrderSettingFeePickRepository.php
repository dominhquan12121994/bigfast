<?php
/**
 * Class OrderExtraRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingFeePickInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderSettingFeePick;

class OrderSettingFeePickRepository extends AbstractEloquentRepository implements OrderSettingFeePickInterface
{
    protected function _getModel()
    {
        return OrderSettingFeePick::class;
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
