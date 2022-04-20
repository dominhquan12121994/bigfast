<?php

/**
 * Class Operators
 * @package App\Modules\Operators\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsTypeInterface;

/* Model */
use App\Modules\Operators\Models\Entities\ContactsType;

class ContactsTypeRepository extends AbstractEloquentRepository implements ContactsTypeInterface
{
    protected function _getModel()
    {
        return ContactsType::class;
    }
}
