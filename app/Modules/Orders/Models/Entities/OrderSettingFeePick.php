<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSettingFeePick extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'order_setting_fee_pick';

    protected $fillable = [
        'min', 
        'max', 
        'value', 
    ];

    public $timestamps = true;
}