<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Reports\Controllers\Admin;

use Session;
use Redirect;
use Validator;
use Exception;
use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AbstractAdminController;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Reports\Models\Services\CodReportServices;
use App\Modules\Reports\Models\Services\ReportServices;

use App\Modules\Orders\Models\Services\CalculatorFeeServices;

class ReportsController extends AbstractAdminController
{
    protected $_codReportServices;
    protected $_shopsInterface;
    protected $_reportServices;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CodReportServices $codReportServices,
                                ReportServices $reportServices,
                                ShopsInterface $shopsInterface)
    {
        parent::__construct();

        $this->_codReportServices = $codReportServices;
        $this->_shopsInterface = $shopsInterface;
        $this->_reportServices = $reportServices;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function codReport(Request $request)
    {
        $you = auth('admin')->user();
        if (!$you->can('action_report_by_cod_view'))
        {
            abort(403);
        }

        if ($request->all() == []) {
            $shopId = 0;
            $beginDate = date('d-m-Y', strtotime('-7 days'));
            $endDate = date('d-m-Y');
            $dateType = 'day';
            $filter = array();

            $request->merge(['shop_id' => $shopId]);
            $request->merge(['begin' => $beginDate]);
            $request->merge(['end' => $endDate]);
            $request->merge(['date_type' => $dateType]);
        }

        if ($request['shop_id'] != 0) {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required|exists:order_shops,id',
                'begin' => 'required',
                'end' => 'required',
                'date_type' => 'required',
            ],
            [
                'shop_id:exists:order_shops,id' => 'Kh??ng t???n t???i shop',
                'begin.required' => 'Vui l??ng nh???p ng??y b???t ?????u',
                'end.required' => 'Vui l??ng nh???p ng??y k???t th??c',
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator->errors())->with($request->all());
            }
        }

        $getDate = array($request['begin'], $request['end']);
        // $diff = date_diff(date_create($getDate[0]), date_create($getDate[1]));
        // if ($diff->days >= 31) {
        //     $date_errors = 'Vui l??ng nh???p kho???ng th???i gian nh??? h??n 31 ng??y';
        //     return Redirect::back()->withInput()->withErrors($date_errors);
        // }

        $shopId = $request['shop_id'];
        $dateType = $request['date_type'];
        $filter['date_range'] = array($request['begin'], $request['end']);

        $payload = array(
            'shop_id' => $shopId,
            'date_range' => $getDate,
            'date_type' => $dateType
        );

        $codReports = $this->_codReportServices->getCodReport($payload);

        $shopInfo = $this->_shopsInterface->getById($shopId);

        return view('Reports::reports.index', [
            'codReports' => $codReports,
            'shopId' => $shopId,
            'shopInfo' => $shopInfo,
            'filter' => $filter,
            'dateType' => $dateType
            ]
        );
    }

    public function byZone(Request $request) {
        $you = auth('admin')->user();
        if (!$you->can('action_report_by_zone_view'))
        {
            abort(403);
        }

        $shop = null;
        $shop_id = 0;

        if ($request->has('shop')) {
            $shop_id = $request->input('shop');

            $shop = $this->_shopsInterface->getById($shop_id);
            if (!$shop && $shop_id != 0) {
                return abort(404);
            }
        }
        $filter['shop'] = $shop_id;
        $endDate = date('Y-m-d');
        $beginDate = date('Y-m-d', strtotime('-14 day'));
        if ($request->has('begin') && $request->has('end')) {
            $beginDate = date('Y-m-d', strtotime($request->input('begin')));
            $endDate = date('Y-m-d', strtotime($request->input('end')));
        }
        $filter['created_range'] = [$beginDate, $endDate];
        $data = $this->_reportServices->reportOrderByDistricts($shop_id, $beginDate, $endDate);

        return view('Reports::reports.zone', ['data' => $data, 'filter' => $filter, 'shop' => $shop]);
    }

    public function byShip(Request $request) {
        $you = auth('admin')->user();
        if (!$you->can('action_report_by_ship_view'))
        {
            abort(403);
        }

        $endDate = date('Y-m-d');
        $beginDate = date('Y-m-d', strtotime('-30 day'));
        if ($request->has('begin') && $request->has('end')) {
            $beginDate = date('Y-m-d', strtotime($request->input('begin')));
            $endDate = date('Y-m-d', strtotime($request->input('end')));
        }
        $filter['created_range'] = [$beginDate, $endDate];

        $type = 'pickup';
        if ( $request->has('type') ) {
            $type = $request->input('type');
        }
        if ($type == 'pickup') {
            $data = $this->_reportServices->reportOrderByShipForAdmin($beginDate, $endDate, 12, 'pickup');
        }
        if ($type == 'shipper') {
            $data = $this->_reportServices->reportOrderByShipForAdmin($beginDate, $endDate, 23, 'shipper');
        }
        if ($type == 'refund') {
            $data = $this->_reportServices->reportOrderByShipForAdmin($beginDate, $endDate, 32, 'refund');
        }

        $filter['type'] = $type;

        return view('Reports::reports.ship', ['filter' => $filter, 'data' => $data->result]);
    }

    public function byStatus(Request $request) {
        $you = auth('admin')->user();
        if (!$you->can('action_report_by_status_view'))
        {
            abort(403);
        }

        $shop = null;
        $shop_id = 0;

        if ($request->has('shop')) {
            $shop_id = $request->input('shop');

            $shop = $this->_shopsInterface->getById($shop_id);
            if (!$shop && $shop_id != 0) {
                return abort(404);
            }
        }

        $endDate = date('Y-m-d');
        $beginDate = date('Y-m-d', strtotime('-30 day'));
        if ($request->has('begin') && $request->has('end')) {
            $beginDate = date('Y-m-d', strtotime($request->input('begin')));
            $endDate = date('Y-m-d', strtotime($request->input('end')));
        }
        $filter['created_range'] = [$beginDate, $endDate];

        $data = [];
        $reportStatus = $this->_reportServices->reportOrderByStatus($shop_id, $beginDate, $endDate);
        if ($reportStatus->status) {
            $result = collect($reportStatus->result);

            $total = $result->sum(function ($val) {
                return array_sum($val);
            });

            $data[] = [
                'name' => 'T???ng ????n h??ng',
                'val' => $total
            ];
            $data[] = [
                'name' => 'Giao h??ng th??nh c??ng',
                'val' => $result->sum(function ($val) {
                    if (isset($val[51])) {
                        return $val[51];
                    }
                })
            ];
            $data[] = [
                'name' => 'Ho??n h??ng th??nh c??ng',
                'val' => $result->sum(function ($val) {
                    if (isset($val[52])) {
                        return $val[52];
                    }
                })
            ];
            $data[] = [
                'name' => '???? ?????i so??t giao h??ng',
                'val' => $result->sum(function ($val) {
                    if (isset($val[81])) {
                        return $val[81];
                    }
                })
            ];
            $data[] = [
                'name' => '???? ?????i so??t ho??n h??ng',
                'val' => $result->sum(function ($val) {
                    if (isset($val[82])) {
                        return $val[82];
                    }
                })
            ];
            $data[] = [
                'name' => 'Giao h??ng kh??ng th??nh c??ng',
                'val' => $result->sum(function ($val) {
                    if (isset($val[24])) {
                        return $val[24];
                    }
                })
            ];
            $data[] = [
                'name' => 'Ho??n h??ng kh??ng th??nh c??ng',
                'val' => $result->sum(function ($val) {
                    if (isset($val[33])) {
                        return $val[33];
                    }
                })
            ];
            $data[] = [
                'name' => '????n h???y',
                'val' => $result->sum(function ($val) {
                    if (isset($val[61])) {
                        return $val[61];
                    }
                })
            ];
            $data[] = [
                'name' => 'Th???t l???c',
                'val' => $result->sum(function ($val) {
                    if (isset($val[71])) {
                        return $val[71];
                    }
                })
            ];
            $data[] = [
                'name' => 'H?? h???ng',
                'val' => $result->sum(function ($val) {
                    if (isset($val[72])) {
                        return $val[72];
                    }
                })
            ];

        }

        return view('Reports::reports.status', ['filter' => $filter, 'data' => $data, 'shop' => $shop ]);
    }

}
