<?php
/**
 * Class OrderShopAddressRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Relations;

use App\Modules\Operators\Models\Entities\ZoneProvinces;
use App\Modules\Operators\Models\Entities\ZoneDistricts;
use App\Modules\Operators\Models\Entities\ZoneWards;

trait OrderShopAddressRelation
{
    public function provinces()
    {
        return $this->belongsTo(ZoneProvinces::class, 'p_id')->withTrashed();
    }

    public function districts()
    {
        return $this->belongsTo(ZoneDistricts::class, 'd_id')->withTrashed();
    }

    public function wards()
    {
        return $this->belongsTo(ZoneWards::class, 'w_id')->withTrashed();
    }
}
