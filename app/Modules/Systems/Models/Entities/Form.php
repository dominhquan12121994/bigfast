<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Entities;

use App\Models\Entities\AbstractModel;

class Form extends AbstractModel
{
    protected $table = 'system_form';

    /**
     * Get the model that owns the Form.
     */
    public function model()
    {
        return $this->belongsTo('App\Modules\Systems\Models\Entities\Models', 'model_id');
    }
}
