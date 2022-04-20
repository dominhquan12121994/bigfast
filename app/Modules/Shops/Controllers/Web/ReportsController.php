<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Web\AbstractWebController;

/**  */
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;

use App\Modules\Reports\Models\Services\ReportServices;

use App\Modules\Reports\Models\Services\CodReportServices;

use App\Modules\Systems\Models\Entities\User;

use App\Modules\Orders\Constants\OrderConstant;

class ReportsController extends AbstractWebController
{
    protected $_shopsInterface;
    protected $_reportServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ShopsInterface $shopsInterface,
                                CodReportServices $codReportServices,
                                ReportServices $reportServices)
    {
        parent::__construct();

        $this->_shopsInterface = $shopsInterface;
        $this->_reportServices = $reportServices;
        $this->_codReportServices = $codReportServices;
    }

    public function byStatus(Request $request) {
        $you = auth('shop')->user();

        $shop_id = $you->id;

        $endDate = date('Y-m-d');
        $beginDate = date('Y-m-d', strtotime('-30 day'));
        if ($request->has('begin') && $request->has('end')) {
            $beginDate = date('Y-m-d', strtotime($request->input('begin')));
            $endDate = date('Y-m-d', strtotime($request->input('end')));
        }
        $filter['created_range'] = [$beginDate, $endDate];

        $dataStatus = [];
        $reportStatus = $this->_reportServices->reportOrderByStatus($shop_id, $beginDate, $endDate);
        if ($reportStatus->status) {
            $result = collect($reportStatus->result);

            $total = $result->sum(function ($val) {
                return array_sum($val);
            });

            $dataStatus[] = [
                'name' => 'Tổng đơn hàng',
                'val' => $total
            ];
            $dataStatus[] = [
                'name' => 'Giao hàng thành công',
                'val' => $result->sum(function ($val) {
                    if (isset($val[51])) {
                        return $val[51];
                    }
                })
            ];
            $dataStatus[] = [
                'name' => 'Hoàn hàng thành công',
                'val' => $result->sum(function ($val) {
                    if (isset($val[52])) {
                        return $val[52];
                    }
                })
            ];
            $dataStatus[] = [
                'name' => 'Đã đối soát giao hàng',
                'val' => $result->sum(function ($val) {
                    if (isset($val[81])) {
                        return $val[81];
                    }
                })
            ];
            $dataStatus[] = [
                'name' => 'Đã đối soát hoàn hàng',
                'val' => $result->sum(function ($val) {
                    if (isset($val[82])) {
                        return $val[82];
                    }
                })
            ];
            $dataStatus[] = [
                'name' => 'Giao hàng không thành công',
                'val' => $result->sum(function ($val) {
                    if (isset($val[24])) {
                        return $val[24];
                    }
                })
            ];
            $dataStatus[] = [
                'name' => 'Hoàn hàng không thành công',
                'val' => $result->sum(function ($val) {
                    if (isset($val[33])) {
                        return $val[33];
                    }
                })
            ];
            $dataStatus[] = [
                'name' => 'Đơn hủy',
                'val' => $result->sum(function ($val) {
                    if (isset($val[61])) {
                        return $val[61];
                    }
                })
            ];
            $dataStatus[] = [
                'name' => 'Thất lạc',
                'val' => $result->sum(function ($val) {
                    if (isset($val[71])) {
                        return $val[71];
                    }
                })
            ];
            $dataStatus[] = [
                'name' => 'Hư hỏng',
                'val' => $result->sum(function ($val) {
                    if (isset($val[72])) {
                        return $val[72];
                    }
                })
            ];

        }

        // dev_quanmd add codReportServices
        $dateType = 'day';
        $shopId = $you->id;
        $begin = date_format(date_create_from_format('Y-m-d', $beginDate), 'd-m-Y');
        $end = date_format(date_create_from_format('Y-m-d', $endDate), 'd-m-Y');

        $payload = array(
            'shop_id' => $shopId,
            'date_range' => array($begin, $end),
            'date_type' => $dateType
        );

        $codReports = $this->_codReportServices->getCodReport($payload);
        // end of dev_quanmd add codReportServices

        $dataZone = $this->_reportServices->reportOrderByDistricts($shop_id, $beginDate, $endDate);

        return view('Shops::report.status', [
            'filter' => $filter,
            'status' => $dataStatus,
            'zone' => $dataZone,
            'dateType' => $dateType,
            'codReports' => $codReports,
            'shopId' => $shopId,
            ]);
    }
}
