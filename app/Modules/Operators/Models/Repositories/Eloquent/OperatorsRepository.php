<?php

/**
 * Class Operators
 * @package App\Modules\Operators\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Operators\Models\Repositories\Contracts\OperatorsInterface;

/* Model */
use App\Modules\Operators\Models\Entities\Operators;

class OperatorsRepository extends AbstractEloquentRepository implements OperatorsInterface
{
    protected function _getModel()
    {
        return Operators::class;
    }
}
