<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Operators\Models\Relations\ContactsHistoryRelation;

class ContactsHistory extends AbstractModel
{
    use SoftDeletes;
    use ContactsHistoryRelation;

    protected $table = 'contacts_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contacts_id', 
        'user_id',
        'type',
        'detail'
    ];

    public $timestamps = true;

    protected $dates = [
        'created_date',
    ];
}
