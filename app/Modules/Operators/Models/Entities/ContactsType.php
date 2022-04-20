<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Operators\Models\Relations\ContactsRelation;

class ContactsType extends AbstractModel
{
    use SoftDeletes;
    use ContactsRelation;

    protected $table = 'contacts_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 
        'name', 
        'sla',
        'level',
        'status',
        'type'
    ];

    public $timestamps = true;

    protected $dates = [
        'created_date',
    ];
}
