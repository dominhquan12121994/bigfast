<?php
/**
 * Class OrderReceiverRelation
 * @package App\Modules\Orders\Models\Relations
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Relations;

use App\Modules\Orders\Models\Entities\OrderService;

trait OrderSettingRelation
{
    public function orderService()
    {
        return $this->belongsTo(OrderService::class, 'service', 'alias')->withTrashed();
    }
}
