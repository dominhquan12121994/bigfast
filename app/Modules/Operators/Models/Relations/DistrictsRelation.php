<?php

namespace App\Modules\Operators\Models\Relations;

use App\Modules\Operators\Models\Entities\ZoneProvinces;
use App\Modules\Operators\Models\Entities\ZoneWards;

trait DistrictsRelation
{
    public function provinces()
    {
        return $this->belongsTo(ZoneProvinces::class, 'p_id')->withTrashed();
    }

    public function wards()
    {
        return $this->hasMany(ZoneWards::class, 'd_id');
    }
}
