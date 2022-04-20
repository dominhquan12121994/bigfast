<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Entities;

use App\Models\Entities\AbstractModel;

class Notes extends AbstractModel
{
    protected $table = 'system_notes';

    /**
     * Get the User that owns the Notes.
     */
    public function user()
    {
        return $this->belongsTo('App\Modules\Systems\Models\Entities\User', 'users_id')->withTrashed();
    }

    /**
     * Get the Status that owns the Notes.
     */
    public function status()
    {
        return $this->belongsTo('App\Modules\Systems\Models\Entities\Status', 'status_id');
    }
}
