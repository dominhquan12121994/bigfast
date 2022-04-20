<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Relations;

use App\Modules\Operators\Models\Entities\ZoneDistricts;
use App\Modules\Operators\Models\Entities\ZoneWards;

trait ProvincesRelation
{
    public function districts()
    {
        return $this->hasMany(ZoneDistricts::class, 'p_id');
    }

    public function wards()
    {
        return $this->hasMany(ZoneWards::class, 'p_id');
    }
}
