<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSettingCod extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'order_setting_cod';

    protected $fillable = [
        'min', 
        'max', 
        'value', 
        'type', 
    ];

    public $timestamps = true;
}