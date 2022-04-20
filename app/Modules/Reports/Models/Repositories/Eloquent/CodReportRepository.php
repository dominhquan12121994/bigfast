<?php

/**
 * Class Reports
 * @package App\Modules\Reports\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Reports\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Reports\Models\Repositories\Contracts\ReportsInterface;
use App\Modules\Orders\Models\Entities\OrderFee;
use App\Modules\Reports\Models\Repositories\Contracts\CodReportInterface;

/* Model */
use App\Modules\Reports\Models\Entities\Reports;

class CodReportRepository extends AbstractEloquentRepository implements CodReportInterface
{
    protected function _getModel()
    {
        return OrderFee::class;
    }
}
