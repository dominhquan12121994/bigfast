<?php

/**
 * Class Reports
 * @package App\Modules\Reports\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Reports\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Reports\Models\Repositories\Contracts\ReportsInterface;

/* Model */
use App\Modules\Reports\Models\Entities\Reports;

class ReportsRepository extends AbstractEloquentRepository implements ReportsInterface
{
    protected function _getModel()
    {
        return Reports::class;
    }
}
