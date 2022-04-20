<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Operators\Models\Relations\ContactsHistoryRelation;

class PrintTemplates extends AbstractModel
{
    use SoftDeletes;
    use ContactsHistoryRelation;

    protected $table = 'order_print_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'page_size', 
        'html',
        'type',
    ];

    public $timestamps = true;

    protected $dates = [
        'created_date',
    ];
}
