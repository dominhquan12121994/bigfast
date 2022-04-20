<?php

/**
 * Class Operators
 * @package App\Modules\Operators\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Operators\Models\Repositories\Contracts\PrintTemplatesInterface;

/* Model */
use App\Modules\Operators\Models\Entities\PrintTemplates;

class PrintTemplatesRepository extends AbstractEloquentRepository implements PrintTemplatesInterface
{
    protected function _getModel()
    {
        return PrintTemplates::class;
    }

    protected function _prepareConditions($conditions, $query)
    {
        $query = parent::_prepareConditions($conditions, $query);

        if (isset($conditions['page_size'])) {
            $query->where('page_size', $conditions['page_size']);
        }

        if (isset($conditions['type'])) {
            $query->where('type', $conditions['type']);
        }

        return $query;
    }
}
