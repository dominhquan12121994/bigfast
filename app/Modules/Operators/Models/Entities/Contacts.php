<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Entities;

use App\Models\Entities\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Operators\Models\Relations\ContactsRelation;
use App\Modules\Operators\Models\Observers\ContactsObserver;

class Contacts extends AbstractModel
{
    use SoftDeletes;
    use ContactsRelation;

    protected $table = 'contacts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lading_code', 
        'user_id', 
        'detail',
        'status',
        'file_path',
        'shop_id', 
        'created_date',
        'order_id', 
        'contacts_type_id', 
        'assign_id',
        'type',
        'last_update',
        'expired',
        'expired_at',
        'done_at'
    ];

    public $timestamps = true;

    protected $dates = [
        'created_date',
    ];

    public static function boot()
    {
        parent::boot();
        self::observe(ContactsObserver::class);
    }
}
