<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Orders\Models\Relations\OrderSettingRelation;

class OrderSetting extends AbstractModel
{
    use SoftDeletes;
    use OrderSettingRelation;

    protected $table = 'order_setting_fee';

    protected $fillable = [
        'route', 
        'service', 
        'result',
        'disable'
    ];

    public $timestamps = true;
}