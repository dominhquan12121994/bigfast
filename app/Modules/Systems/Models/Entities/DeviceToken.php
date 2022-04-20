<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class DeviceToken
 * @package App\Modules\Systems\Models
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Systems\Models\Entities;

/* Contracts */
use App\Models\Entities\AbstractModel;

/* Libs */
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceToken extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'system_device_token';

    protected $fillable = array(
        'user_type',
        'user_id',
        'device_type',
        'device_token',
    );

    protected $hidden = array(
        'deleted_at'
    );
}
