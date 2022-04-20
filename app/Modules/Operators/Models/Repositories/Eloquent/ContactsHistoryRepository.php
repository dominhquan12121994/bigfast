<?php

/**
 * Class Operators
 * @package App\Modules\Operators\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsHistoryInterface;

/* Model */
use App\Modules\Operators\Models\Entities\ContactsHistory;

class ContactsHistoryRepository extends AbstractEloquentRepository implements ContactsHistoryInterface
{
    protected function _getModel()
    {
        return ContactsHistory::class;
    }

    protected function _prepareConditions($conditions, $query)
    {
        $query = parent::_prepareConditions($conditions, $query);

        if (isset($conditions['contacts_id'])) {
            $contacts_id = $conditions['contacts_id'];
            $query->where('contacts_id', $contacts_id);
        }

        if (isset($conditions['search'])) {
            $search = '%'.$conditions['search'].'%';
            $query->where('detail', 'LIKE' , $search);
        }

        return $query;
    }
}
