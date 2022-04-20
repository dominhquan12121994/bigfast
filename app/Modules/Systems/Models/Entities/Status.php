<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Entities;

use App\Models\Entities\AbstractModel;

class Status extends AbstractModel
{
    protected $table = 'system_status';

    public $timestamps = false; 
    /**
     * Get the notes for the status.
     */
    public function notes()
    {
        return $this->hasMany('App\Modules\Systems\Models\Entities\Notes');
    }
}
