<?php

/**
 * Class OrdersRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Relations;

use App\Modules\Operators\Models\Entities\ZoneDistricts;

trait ReportsByZoneRelation
{
    public function districts()
    {
        return $this->belongsTo(ZoneDistricts::class, 'd_id')->withTrashed();
    }
}
