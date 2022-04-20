<?php

/**
 * Class Operators
 * @package App\Modules\Operators\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsRefuseInterface;

/* Model */
use App\Modules\Operators\Models\Entities\ContactsRefuse;

class ContactsRefuseRepository extends AbstractEloquentRepository implements ContactsRefuseInterface
{
    protected function _getModel()
    {
        return ContactsRefuse::class;
    }

    protected function _prepareConditions($conditions, $query)
    {
        $query = parent::_prepareConditions($conditions, $query);

        if (isset($conditions['contact_id'])) {
            $query->where('contact_id', $conditions['contact_id']);
        }

        if (isset($conditions['user_id'])) {
            $query->where('user_id', $conditions['user_id']);
        }

        return $query;
    }
}
