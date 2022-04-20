<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use App\Models\Entities\AbstractModel;
use App\Modules\Orders\Models\Relations\OrderExtraRelation;

class OrderExtra extends AbstractModel
{
    use OrderExtraRelation;

    protected $table = 'order_extras';

    protected $fillable = [
        'id', 'note1', 'note2', 'client_code', 'receiver_phone', 'receiver_name', 'receiver_address', 'receiver_p_id', 'receiver_d_id', 'receiver_w_id', 'expect_pick', 'expect_receiver'
    ];

    public $timestamps = false;
}