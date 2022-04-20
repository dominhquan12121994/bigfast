<?php
/**
 * Class OrderExtraRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingFeeInsuranceInterface;

/* Model */
use App\Modules\Orders\Models\Entities\OrderSettingFeeInsurance;

class OrderSettingFeeInsuranceRepository extends AbstractEloquentRepository implements OrderSettingFeeInsuranceInterface
{
    protected function _getModel()
    {
        return OrderSettingFeeInsurance::class;
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

        if (isset($conditions['insurance'])) {
            $insurance = $conditions['insurance'];
            $query->where('min', '<=', $insurance);
            $query->where('max', '>=', $insurance);
        }
        
        return $query;
    }
}
