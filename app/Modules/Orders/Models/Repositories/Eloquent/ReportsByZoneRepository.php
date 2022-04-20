<?php
/**
 * Class ReportsByZoneRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Orders\Models\Repositories\Contracts\ReportsByZoneInterface;

/* Model */
use App\Modules\Orders\Models\Entities\ReportsByZone;

class ReportsByZoneRepository extends AbstractEloquentRepository implements ReportsByZoneInterface
{
    protected function _getModel()
    {
        return ReportsByZone::class;
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

        if (isset($conditions['shop_id'])) {
            $shop_id = $conditions['shop_id'];
            $query->where('shop_id', $shop_id);
        }

        if (isset($conditions['created_range'])) {
            $range = $conditions['created_range'];
            $query->whereBetween('date', $range);
        }

        return $query;
    }
}
