<?php

/**
 * Class IndexController
 * @package App\Modules\Systems\Controllers\Api
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Systems\Controllers\Api;

use App\Http\Controllers\Api\AbstractApiController;

use Validator;
use Illuminate\Http\Request;

use App\Modules\Orders\Constants\OrderConstant;
use App\Modules\Systems\Models\Entities\CallHistory;

class IndexController extends AbstractApiController
{
    public function settings()
    {
        $payload = array(
            "app" => array(
                "name" => "BigFast Shipper",
                "version" => "1.0.0",
                "upgrade" => true
            ),
            "constant" => array(
                "orderStatusChange" => [
                    [
                        'status' => 12,
                        'name' => 'Đang lấy hàng',
                        'next' => [
                            [
                                'status' => 13,
                                'name' => 'Lấy hàng không thành công',
                                'elements' => [
                                    [
                                        'name' => 'fail_note',
                                        'type' => 'text'
                                    ]
                                ]
                            ],
                            [
                                'status' => 61,
                                'name' => 'Huỷ đơn hàng',
                                'elements' => [
                                    [
                                        'name' => 'cancel_reason',
                                        'type' => 'select',
                                        'options' => [
                                            "Shop yêu cầu huỷ hàng",
                                            "Lý do khác"
                                        ]
                                    ],
                                    [
                                        'name' => 'cancel_note',
                                        'type' => 'text'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'status' => 13,
                        'name' => 'Lấy hàng không thành công',
                        'next' => [
                            [
                                'status' => 12,
                                'name' => 'Đang lấy hàng',
                                'elements' => [
                                    [
                                        'name' => 'select_shipper',
                                        'type' => 'defaults',
                                        'defaults' => -1
                                    ]
                                ]
                            ],
                            [
                                'status' => 61,
                                'name' => 'Huỷ đơn hàng',
                                'elements' => [
                                    [
                                        'name' => 'cancel_reason',
                                        'type' => 'select',
                                        'options' => [
                                            "Shop yêu cầu huỷ hàng",
                                            "Lý do khác"
                                        ]
                                    ],
                                    [
                                        'name' => 'cancel_note',
                                        'type' => 'text'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'status' => 23,
                        'name' => 'Đang giao hàng',
                        'next' => [
                            [
                                'status' => 41,
                                'name' => 'Chờ xác nhận giao lại',
                                'elements' => []
                            ],
                            [
                                'status' => 51,
                                'name' => 'Giao hàng thành công',
                                'elements' => []
                            ],
//                            [
//                                'status' => 34,
//                                'name' => 'Chờ duyệt hoàn',
//                                'elements' => []
//                            ],
                            [
                                'status' => 71,
                                'name' => 'Thất lạc',
                                'elements' => [
                                    [
                                        'name' => 'missing_note',
                                        'type' => 'text'
                                    ]
                                ]
                            ],
                            // [
                            //     'status' => 72,
                            //     'name' => 'Hư hỏng',
                            //     'elements' => [
                            //         [
                            //             'name' => 'damaged_note',
                            //             'type' => 'text'
                            //         ]
                            //     ]
                            // ]
                        ]
                    ],
//                    [
//                        'status' => 24,
//                        'name' => 'Giao hàng không thành công',
//                        'next' => [
//                            [
//                                'status' => 34,
//                                'name' => 'Chờ duyệt hoàn',
//                                'elements' => []
//                            ]
//                        ]
//                    ],
                    [
                        'status' => 34,
                        'name' => 'Chờ duyệt hoàn',
                        'next' => []
                    ],
                    [
                        'status' => 32,
                        'name' => 'Đang hoàn hàng',
                        'next' => [
//                            [
//                                'status' => 33,
//                                'name' => 'Hoàn hàng không thành công',
//                                'elements' => [
//                                    [
//                                        'name' => 'fail_note',
//                                        'type' => 'text'
//                                    ]
//                                ]
//                            ],
//                            [
//                                'status' => 52,
//                                'name' => 'Hoàn hàng thành công',
//                                'elements' => []
//                            ],
                            [
                                'status' => 71,
                                'name' => 'Thất lạc',
                                'elements' => [
                                    [
                                        'name' => 'missing_note',
                                        'type' => 'text'
                                    ]
                                ]
                            ],
//                            [
//                                'status' => 72,
//                                'name' => 'Hư hỏng',
//                                'elements' => [
//                                    [
//                                        'name' => 'damaged_note',
//                                        'type' => 'text'
//                                    ]
//                                ]
//                            ]
                        ]
                    ],
//                    [
//                        'status' => 33,
//                        'name' => 'Hoàn hàng không thành công',
//                        'next' => [
//                            [
//                                'status' => 52,
//                                'name' => 'Hoàn hàng thành công',
//                                'elements' => []
//                            ]
//                        ]
//                    ],
                    [
                        'status' => 41,
                        'name' => 'Chờ xác nhận giao lại',
                        'next' => [
                            [
                                'status' => 23,
                                'name' => 'Đang giao hàng',
                                'elements' => [
                                    [
                                        'name' => 'select_shipper',
                                        'type' => 'defaults',
                                        'defaults' => -1
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'status' => 61,
                        'name' => 'Đơn huỷ',
                        'next' => []
                    ],
                    [
                        'status' => 71,
                        'name' => 'Thất lạc',
                        'next' => [
                        ]
                    ],
                    [
                        'status' => 72,
                        'name' => 'Hư hỏng',
                        'next' => []
                    ],
                ]
            )
        );
        return $this->_responseSuccess('Success', $payload);
    }

    public function callHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logs' => 'required|array|min:1'
        ]);

        if ($validator->fails()) {
            return $this->_responseError('Chưa có dữ liệu cuộc gọi');
        }

        CallHistory::create([
            'user_id' => $request->user()->id,
            'logs' => json_encode($request->input('logs')),
        ]);

        return $this->_responseSuccess('Success');
    }
}
