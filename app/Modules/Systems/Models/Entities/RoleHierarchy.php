<?php

namespace App\Modules\Systems\Models\Entities;

use App\Models\Entities\AbstractModel;

class RoleHierarchy extends AbstractModel
{
    protected $table = 'system_role_hierarchy';

    public $timestamps = false;
    
}
