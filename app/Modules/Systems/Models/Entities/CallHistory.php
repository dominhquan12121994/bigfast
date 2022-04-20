<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Class CallHistory
 * @package App\Modules\Systems\Models
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Systems\Models\Entities;

/* Contracts */
use App\Models\Entities\AbstractModel;

use Illuminate\Database\Eloquent\SoftDeletes;

class CallHistory extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'system_call_history';

    protected $fillable = array(
        'user_id',
        'logs',
    );

    protected $hidden = array(
        'deleted_at'
    );
}
