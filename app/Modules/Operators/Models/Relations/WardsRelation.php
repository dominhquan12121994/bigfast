<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Relations;

use App\Modules\Operators\Models\Entities\ZoneProvinces;
use App\Modules\Operators\Models\Entities\ZoneDistricts;

trait WardsRelation
{
    public function provinces()
    {
        return $this->belongsTo(ZoneProvinces::class, 'p_id')->withTrashed();
    }

    public function districts()
    {
        return $this->belongsTo(ZoneDistricts::class, 'd_id')->withTrashed();
    }
}
