<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Operators\Models\Relations\PostOfficesRelation;

class PostOffice extends AbstractModel
{
    use SoftDeletes;
    use PostOfficesRelation;

    protected $table = 'system_post_offices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['p_id', 'd_id', 'w_id', 'name'];

    protected $dates = [
        'deleted_at'
    ];
}
