<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Reports\Controllers\Api;

use Session;
use Redirect;
use Validator;
use Exception;
use Throwable;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AbstractApiController;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Reports\Models\Services\CodReportServices;
use App\Modules\Reports\Models\Services\ReportServices;

use App\Modules\Orders\Models\Services\CalculatorFeeServices;

class ReportsController extends AbstractApiController
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
                                ShopsInterface $shopsInterface,
                                ReportServices $reportServices)
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
        if ($request['shop_id'] != 0) {
            $validator = Validator::make($request->all(), [
                'shop_id' => 'required|exists:order_shops,id',
                'date_type' => 'required',
                'date_range' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->_responseError($validator->errors());
            }
        }

        $shop_id = $request['shop_id'];
        $getDate = explode(' - ', $request['date_range']);
        $dateType = $request['date_type'];

        $payload = array(
            'shop_id' => $shop_id,
            'date_range' => $getDate,
            'date_type' => $dateType
        );

        $codReports = $this->_codReportServices->getCodReport($payload);

        return $this->_responseSuccess('Success', $codReports);
    }

    public function shipper(Request $request) {
        $data = [];
        $details = [];

        $validator = Validator::make($request->all(), [
            'begin' => 'required|date|before_or_equal:end',
            'end' => 'required|date|before:tomorrow',
            'type' => 'required|in:pickup,shipper,refund'
        ], [
            'begin.required' => 'Ng??y b???t ?????u b???t bu???c',
            'begin.required' => 'Ng??y k???t th??c b???t bu???c',
            'date' => 'Sai ?????nh d???ng ng??y',
            'begin.before_or_equal' => 'Ng??y b???t ?????u ph???i nh??? h??n ho???c b???ng ng??y k???t th??c',
            'begin.before' => 'Ng??y k???t th??c ph???i nh??? h??n ho???c b???ng ng??y hi???n t???i',
            'in' => 'Sai vai tr??'
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors());
        }

        $you = $request->user();

        if ( !$you ) {
            return $this->_responseError('Vui l??ng ????ng nh???p l???i');
        }

        $aryRole = ['pickup', 'shipper', 'refund'];
        $role_user = $you->getRoleNames()[0];

        if ( !in_array($role_user, $aryRole) ) {
            return $this->_responseError('B???n kh??ng ph???i nh??n vi??n kho');
        }

        $endDate = date('Y-m-d');
        $beginDate = date('Y-m-d', strtotime('-30 day'));
        if ($request->has('begin') && $request->has('end')) {
            $beginDate = date('Y-m-d', strtotime($request->input('begin')));
            $endDate = date('Y-m-d', strtotime($request->input('end')));
        }

        $dateBegin = date( 'Ymd', strtotime($request->input('begin')) );
        $firstDateEnd = date( 'Ym', strtotime($request->input('end')) ) . '01';
        if ( (int)$dateBegin < (int)$firstDateEnd ) {
            return $this->_responseError('Kho???ng ng??y ph???i c??ng trong 1 th??ng');
        }

        $role_type = $request->input('type');

        if ( $role_type == 'shipper' ) {
            $data['totals'] = [
                [
                    'name' => 'T???ng ????n',
                    'value' => 0,
                ],
                [
                    'name' => 'Giao th??nh c??ng',
                    'value' => 0,
                ],
                [
                    'name' => 'Giao kh??ng th??nh c??ng',
                    'value' => 0,
                ],
            ];
            $data['shops'] = [];

            $reportOrderByShips = $this->_reportServices->reportOrderByShip($beginDate, $endDate, 23, 'shipper', $you->id);
            foreach ( $reportOrderByShips->result as $user_key => $user_val) {
                $total = array_sum(array_column($user_val, 'total'));
                $success = array_sum(array_column($user_val, 'success'));
                $fail = array_sum(array_column($user_val, 'fail'));
                $data['totals'] = [
                    [
                        'name' => 'T???ng ????n',
                        'value' => $total,
                    ],
                    [
                        'name' => 'Giao th??nh c??ng',
                        'value' => $success,
                    ],
                    [
                        'name' => 'Giao kh??ng th??nh c??ng',
                        'value' => $fail,
                    ],
                ];
                foreach ($user_val as $shop_key => $shop_val ) {
                    $details[] = [
                        'name' => $shop_val['shop'],
                        'details' => [
                            [
                                'name' => 'T???ng ????n',
                                'value' => $shop_val['total'],
                            ],
                            [
                                'name' => 'Giao th??nh c??ng',
                                'value' => $shop_val['success'],
                            ],
                            [
                                'name' => 'Giao kh??ng th??nh c??ng',
                                'value' => $shop_val['fail'],
                            ],
                        ],
                    ];
                }
                $data['shops'] = $details;
            }
        }
        if ( $role_type == 'pickup' ) {
            $data['totals'] = [
                [
                    'name' => 'T???ng ????n',
                    'value' => 0,
                ],
                [
                    'name' => 'L???y th??nh c??ng',
                    'value' => 0,
                ],
                [
                    'name' => 'L???y kh??ng th??nh c??ng',
                    'value' => 0,
                ],
            ];
            $data['shops'] = [];

            $reportOrderByShips = $this->_reportServices->reportOrderByShip($beginDate, $endDate, 12, 'pickup', $you->id);
            foreach ( $reportOrderByShips->result as $user_key => $user_val) {
                $total = array_sum(array_column($user_val, 'total'));
                $success = array_sum(array_column($user_val, 'success'));
                $fail = array_sum(array_column($user_val, 'fail'));
                $data['totals'] = [
                    [
                        'name' => 'T???ng ????n',
                        'value' => $total,
                    ],
                    [
                        'name' => 'L???y th??nh c??ng',
                        'value' => $success,
                    ],
                    [
                        'name' => 'L???y kh??ng th??nh c??ng',
                        'value' => $fail,
                    ],
                ];
                foreach ($user_val as $shop_key => $shop_val ) {
                    $details[] = [
                        'name' => $shop_val['shop'],
                        'details' => [
                            [
                                'name' => 'T???ng ????n',
                                'value' => $shop_val['total'],
                            ],
                            [
                                'name' => 'L???y th??nh c??ng',
                                'value' => $shop_val['success'],
                            ],
                            [
                                'name' => 'L???y kh??ng th??nh c??ng',
                                'value' => $shop_val['fail'],
                            ],
                        ],
                    ];
                }
                $data['shops'] = $details;
            }
        }
        if ( $role_type == 'refund' ) {
            $data['totals'] = [
                [
                    'name' => 'T???ng ????n',
                    'value' => 0,
                ],
                [
                    'name' => 'Ho??n th??nh c??ng',
                    'value' => 0,
                ],
                [
                    'name' => 'Ho??n kh??ng th??nh c??ng',
                    'value' => 0,
                ],
            ];
            $data['shops'] = [];

            $reportOrderByShips = $this->_reportServices->reportOrderByShip($beginDate, $endDate, 32, 'refund', $you->id);
            foreach ( $reportOrderByShips->result as $user_key => $user_val) {
                $total = array_sum(array_column($user_val, 'total'));
                $success = array_sum(array_column($user_val, 'success'));
                $fail = array_sum(array_column($user_val, 'fail'));
                $data['totals'] = [
                    [
                        'name' => 'T???ng ????n',
                        'value' => $total,
                    ],
                    [
                        'name' => 'Ho??n th??nh c??ng',
                        'value' => $success,
                    ],
                    [
                        'name' => 'Ho??n kh??ng th??nh c??ng',
                        'value' => $fail,
                    ],
                ];
                foreach ($user_val as $shop_key => $shop_val ) {
                    $details[] = [
                        'name' => $shop_val['shop'],
                        'details' => [
                            [
                                'name' => 'T???ng ????n',
                                'value' => $shop_val['total'],
                            ],
                            [
                                'name' => 'Ho??n th??nh c??ng',
                                'value' => $shop_val['success'],
                            ],
                            [
                                'name' => 'Ho??n kh??ng th??nh c??ng',
                                'value' => $shop_val['fail'],
                            ],
                        ],
                    ];
                }
                $data['shops'] = $details;
            }
        }

        return $this->_responseSuccess('Success', $data);
    }

    public function listShopByShip(Request $request) {
        $validator = Validator::make($request->all(), [
            'begin' => 'required|date|before_or_equal:end',
            'end' => 'required|date|before:tomorrow',
            'type' => 'required|in:pickup,shipper,refund',
            'user_id' => 'required|exists:system_users,id'
        ], [
            'begin.required' => 'Ng??y b???t ?????u b???t bu???c',
            'begin.required' => 'Ng??y k???t th??c b???t bu???c',
            'date' => 'Sai ?????nh d???ng ng??y',
            'begin.before_or_equal' => 'Ng??y b???t ?????u ph???i nh??? h??n ho???c b???ng ng??y k???t th??c',
            'begin.before' => 'Ng??y k???t th??c ph???i nh??? h??n ho???c b???ng ng??y hi???n t???i',
            'in' => 'Sai vai tr??'
        ]);

        if ($validator->fails()) {
            return $this->_responseError($validator->errors()->first());
        }

        $endDate = date('Y-m-d');
        $beginDate = date('Y-m-d', strtotime('-30 day'));
        if ($request->has('begin') && $request->has('end')) {
            $beginDate = date('Y-m-d', strtotime($request->input('begin')));
            $endDate = date('Y-m-d', strtotime($request->input('end')));
        }

        $type = $request->input('type', 'pickup');
        $user_id = $request->input('user_id');
        
        $filter['created_range'] = [$beginDate, $endDate];
        $filter['type'] = $type;

        if ($type == 'pickup') {
            $data = $this->_reportServices->reportOrderByShip($beginDate, $endDate, 12, 'pickup', $user_id);
            $data->result = array_values($data->result);
        }
        if ($type == 'shipper') {
            $data = $this->_reportServices->reportOrderByShip($beginDate, $endDate, 23, 'shipper', $user_id);
            $data->result = array_values($data->result);
        }
        if ($type == 'refund') {
            $data = $this->_reportServices->reportOrderByShip($beginDate, $endDate, 32, 'refund', $user_id);
            $data->result = array_values($data->result);
        }


        return $this->_responseSuccess('Success', $data);
    }

}
