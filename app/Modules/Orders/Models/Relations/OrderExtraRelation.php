<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class OrderExtraRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Relations;

use App\Modules\Operators\Models\Entities\ZoneProvinces;
use App\Modules\Operators\Models\Entities\ZoneDistricts;
use App\Modules\Operators\Models\Entities\ZoneWards;

trait OrderExtraRelation
{
    public function provinces()
    {
        return $this->belongsTo(ZoneProvinces::class, 'receiver_p_id')->withTrashed();
    }

    public function districts()
    {
        return $this->belongsTo(ZoneDistricts::class, 'receiver_d_id')->withTrashed();
    }

    public function wards()
    {
        return $this->belongsTo(ZoneWards::class, 'receiver_w_id')->withTrashed();
    }
}
