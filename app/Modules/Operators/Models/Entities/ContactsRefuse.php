<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactsRefuse extends AbstractModel
{
    use SoftDeletes;

    protected $table = 'contacts_reject';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_id', 
        'user_id',
        'reason'
    ];

    public $timestamps = true;

    protected $dates = [
        'created_date',
    ];
}
