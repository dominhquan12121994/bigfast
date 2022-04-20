<?php

namespace App\Modules\Orders\Constants;

/**
 * Class OrderConstant
 * @package App\Modules\Orders\Constants
 * @author HuyDien <huydien.it@gmail.com>
 */
class OrderConstant
{
    const maxTotalOrder = 966905659;

    const feeSettings = array(
        'forward' => 1000,
        'store' => 1000,
        'refund' => 1000,
        'transfer' => 1000,
        'change_info' => 1000,
    );

    const fee_types = array(
        'transport' => 'Phí vận chuyển',
        'pick' => 'Phí lấy hàng',
        'forward' => 'Phí chuyển tiếp',
        'change_info' => 'Phí thay đổi thông tin',
        'store' => 'Phí lưu kho',
        'refund' => 'Phí chuyển hoàn',
        'transfer' => 'Phí chuyển tiền',
        'insurance' => 'Phí bảo hiểm',
        'cod' => 'Phí thu hộ',
        'total_cod' => 'Số tiền thu hộ',
        'refund_cod' => 'Hoàn phí thu hộ',
        'refund_transport' => 'Hoàn phí vận chuyển'
    );

    const notes = array(
        'choxemhang' => 'Cho xem hàng cho thử',
        'choxemhangkhongthu' => 'Cho xem hàng không thử',
        'khongchoxemhang' => 'Không cho xem hàng'
    );

    const payfees = array(
        'payfee_sender' => 'Bên gửi trả phí',
        'payfee_receiver' => 'Bên nhận trả phí'
    );

    const weekday = [
        'monday' => 'Thứ hai',
        'tuesday' => 'Thứ ba',
        'wednesday' => 'Thứ tư',
        'thursday' => 'Thứ năm',
        'friday' => 'Thứ sáu',
        'saturday' => 'Thứ bảy',
        'sunday' => 'Chủ nhật'
    ];

    const actions = [
        'create_order' => 'Tạo đơn hàng',
        'update_order' => 'Cập nhật đơn hàng',
        'assign_refund' => 'Gán ship chuyển hoàn',
        'refund_fail' => 'Chuyển hoàn không thành công',
        'assign_shipper_pick' => 'Gán ship lấy hàng',
        'assign_shipper_send' => 'Gán ship giao hàng',
        'cancel' => 'Huỷ đơn hàng',
        'send_fail' => 'Giao hàng không thành công',
        'set_refund' => 'Chuyển hoàn',
        'confirm_refund' => 'Chờ chuyển hoàn',
        'approval_refund' => 'Duyệt chuyển hoàn',
        'pick_fail' => 'Lấy hàng không thành công',
        'send_success' => 'Giao hàng thành công',
        'refund_success' => 'Chuyển hoàn thành công',
        'confirm_resend' => 'Xác nhận giao lại',
        'pick_success' => 'Lấy hàng thành công',
        'warehouse_refund' => 'Hàng hoàn về kho',
        'warehouse' => 'Nhập kho',
        'missing' => 'Thất lạc',
        'damaged' => 'Hư hỏng',
        'missing_confirm' => 'Đã thoả thuận thất lạc',
        'damaged_confirm' => 'Đã thoả thuận hư hỏng',
        'store' => 'Lưu kho',
        'collect_money' => 'Chờ đối soát',
        'reconcile_send' => 'Đã đối soát giao hàng',
        'reconcile_refund' => 'Đã đối soát chuyển hoàn',
        'reconcile_missing' => 'Đã đối soát thất lạc',
        'reconcile_damaged' => 'Đã đối soát hư hỏng',
    ];

    const order_fee_types = [
        'incurred_total_cod' => 'Tiền thu hộ phát sinh',
        'incurred_money_indemnify' => 'Tiền bồi thường phát sinh',
        'incurred_fee_transport' => 'Phí vận chuyển phát sinh',
        'incurred_fee_insurance' => 'Phí bảo hiểm phát sinh',
        'incurred_fee_cod' => 'Phí thu hộ phát sinh',
        'incurred_fee_refund' => 'Phí chuyển hoàn phát sinh',
        'incurred_fee_store' => 'Phí lưu kho phát sinh',
        'incurred_fee_change_info' => 'Phí thay đổi thông tin phát sinh',
        'incurred_fee_transfer' => 'Phí chuyển khoản phát sinh',
        'incurred_fee_pick' => 'Phí lấy hàng phát sinh',
        'incurred_fee_forward' => 'Phí chuyển tiếp phát sinh',
        'incurred_refund_cod' => 'Hoàn phí cod phát sinh',
        'incurred_refund_transport' => 'Hoàn phí vận chuyển phát sinh',
    ];

    // color: primary,secondary,success,danger,warning,info,light,dark
    const status = [
        0 => [
            'name' => 'Chưa tiếp nhận',
            'detail' => []
        ],
        1 => [
            'name' => 'Chờ bàn giao',
            'detail' => [
                11 => [
                    'name' => 'Chờ lấy hàng',
                    'color' => 'warning',
                    'next' => [
                        12 => 'Gán nhân viên kho',
                        61 => 'Huỷ đơn hàng',
                    ]
                ],
                12 => [
                    'name' => 'Đang lấy hàng',
                    'color' => 'info',
                    'next' => [
                        13 => 'Lấy hàng không thành công',
//                        21 => 'Lấy hàng thành công',
                        22 => 'Nhập kho',
                        61 => 'Huỷ đơn hàng',
                    ]
                ],
                13 => [
                    'name' => 'Lấy hàng không thành công',
                    'color' => 'info',
                    'next' => [
//                        21 => 'Lấy hàng thành công',
                        12 => 'Đang lấy hàng',
//                        22 => 'Nhập kho',
                        61 => 'Huỷ đơn hàng',
                    ]
                ]
            ],
        ],
        2 => [
            'name' => 'Giao hàng',
            'detail' => [
//                21 => [
//                    'name' => 'Lấy hàng thành công',
//                    'color' => 'info',
//                    'next' => [
//                        22 => 'Nhập kho',
//                        71 => 'Thất lạc',
//                        72 => 'Hư hỏng',
//                    ]
//                ],
                22 => [
                    'name' => 'Nhập kho',
                    'color' => 'info',
                    'next' => [
                        23 => 'Điều phối giao hàng',
                        22 => 'Chuyển kho',
                        25 => 'Lưu kho',
//                        32 => 'Gán nhân viên hoàn',
//                        31 => 'Chuyển hoàn',
//                        34 => 'Chờ duyệt hoàn',
                        71 => 'Thất lạc',
                        72 => 'Hư hỏng',
                    ]
                ],
                23 => [
                    'name' => 'Đang giao hàng',
                    'color' => 'info',
                    'next' => [
                        41 => 'Chờ xác nhận giao lại',
//                        24 => 'Giao hàng không thành công',
//                        31 => 'Chuyển hoàn',
//                        34 => 'Chờ duyệt hoàn',
                        51 => 'Giao hàng thành công',
                        71 => 'Thất lạc',
//                        72 => 'Hư hỏng',
                    ]
                ],
//                24 => [
//                    'name' => 'Giao hàng không thành công',
//                    'color' => 'info',
//                    'next' => [
//                        25 => 'Lưu kho',
//                        34 => 'Chờ duyệt hoàn',
//                        41 => 'Chờ xác nhận giao lại',
//                    ]
//                ],
                25 => [
                    'name' => 'Lưu kho',
                    'color' => 'info',
                    'next' => [
                        23 => 'Điều phối giao hàng',
                        31 => 'Chuyển hoàn',
                        41 => 'Chờ xác nhận giao lại',
                    ]
                ]
            ]
        ],
        4 => [
            'name' => 'Chờ xác nhận giao lại',
            'detail' => [
                41 => [
                    'name' => 'Chờ xác nhận giao lại',
                    'color' => 'primary',
                    'next' => [
                        23 => 'Đang giao hàng',
                        22 => 'Nhập kho',
                        34 => 'Chờ duyệt hoàn',
                        51 => 'Giao hàng thành công'
                    ]
                ]
            ]
        ],
        3 => [
            'name' => 'Hoàn hàng',
            'detail' => [
                34 => [
                    'name' => 'Chờ duyệt hoàn',
                    'color' => 'warning',
                    'next' => [
                        23 => 'Đang giao hàng',
                        35 => 'Duyệt hoàn',
                    ]
                ],
                35 => [
                    'name' => 'Duyệt hoàn',
                    'color' => 'warning',
                    'next' => [
                        36 => 'Hoàn nhập kho',
                    ]
                ],
                31 => [
                    'name' => 'Chuyển hoàn',
                    'color' => 'info',
                    'next' => [
                        32 => 'Gán nhân viên hoàn',
                        41 => 'Chờ xác nhận giao lại',
                        71 => 'Thất lạc',
                        72 => 'Hư hỏng',
                    ]
                ],
                36 => [
                    'name' => 'Hoàn nhập kho',
                    'color' => 'info',
                    'next' => [
                        32 => 'Gán nhân viên hoàn',
                    ]
                ],
                32 => [
                    'name' => 'Đang hoàn hàng',
                    'color' => 'dark',
                    'next' => [
//                        33 => 'Hoàn hàng không thành công',
                        82 => 'Đã đối soát hoàn hàng',
                        84 => 'Đã đối soát hư hỏng',
                        71 => 'Thất lạc',
                    ]
                ],
//                33 => [
//                    'name' => 'Hoàn hàng không thành công',
//                    'color' => 'dark',
//                    'next' => [
//                        25 => 'Lưu kho',
//                        52 => 'Hoàn hàng thành công',
//                    ]
//                ]
            ]
        ],
        5 => [
            'name' => 'Giao thành công',
            'detail' => [
                51 => [
                    'name' => 'Giao hàng thành công',
                    'color' => 'success',
                    'next' => [
                        91 => 'Chờ đối soát',
                    ]
                ],
//                52 => [
//                    'name' => 'Hoàn hàng thành công',
//                    'color' => 'success',
//                    'next' => [
//                        82 => 'Đã đối soát hoàn hàng',
//                    ]
//                ],
            ]
        ],
        9 => [
            'name' => 'Chờ đối soát',
            'detail' => [
                91 => [
                    'name' => 'Chờ đối soát',
                    'color' => 'success',
                    'next' => []
                ],
            ]
        ],
        8 => [
            'name' => 'Đối soát',
            'detail' => [
                81 => [
                    'name' => 'Đã đối soát giao hàng',
                    'color' => 'success',
                    'next' => []
                ],
                82 => [
                    'name' => 'Đã đối soát hoàn hàng',
                    'color' => 'success',
                    'next' => []
                ],
                83 => [
                    'name' => 'Đã đối soát thất lạc',
                    'color' => 'success',
                    'next' => []
                ],
                84 => [
                    'name' => 'Đã đối soát hư hỏng',
                    'color' => 'success',
                    'next' => []
                ],
            ]
        ],
        6 => [
            'name' => 'Đơn huỷ',
            'detail' => [
                61 => [
                    'name' => 'Đơn huỷ',
                    'color' => 'danger',
                    'next' => []
                ],
            ]
        ],
        7 => [
            'name' => 'Thất lạc - hư hỏng',
            'detail' => [
                71 => [
                    'name' => 'Thất lạc',
                    'color' => 'danger',
                    'next' => [
                        73 => 'Đã thoả thuận thất lạc',
                    ]
                ],
                72 => [
                    'name' => 'Hư hỏng',
                    'color' => 'danger',
                    'next' => [
                        74 => 'Đã thoả thuận hư hỏng',
                    ]
                ],
                73 => [
                    'name' => 'Đã thoả thuận thất lạc',
                    'color' => 'success',
                    'next' => [
                        83 => 'Đã đối soát thất lạc',
                    ]
                ],
                74 => [
                    'name' => 'Đã thoả thuận hư hỏng',
                    'color' => 'success',
                    'next' => [
                        32 => 'Gán nhân viên hoàn',
                        84 => 'Đã đối soát hư hỏng',
                    ]
                ],
            ]
        ]
    ];

    const statusCoordinator = [
        0 => [
            'name' => 'Chưa tiếp nhận',
            'detail' => []
        ],
        1 => [
            'name' => 'Chờ bàn giao',
            'detail' => [
                11 => [
                    'name' => 'Chờ lấy hàng',
                    'color' => 'warning',
                    'next' => [
                        12 => 'Gán nhân viên kho',
                        // 61 => 'Huỷ đơn hàng',
                    ]
                ],
                12 => [
                    'name' => 'Đang lấy hàng',
                    'color' => 'info',
                    'next' => [
                        // 13 => 'Lấy hàng không thành công',
//                        21 => 'Lấy hàng thành công',
                        22 => 'Nhập kho',
                        // 61 => 'Huỷ đơn hàng',
                    ]
                ],
                13 => [
                    'name' => 'Lấy hàng không thành công',
                    'color' => 'info',
                    'next' => [
//                        21 => 'Lấy hàng thành công',
                        12 => 'Đang lấy hàng',
//                        22 => 'Nhập kho',
                        // 61 => 'Huỷ đơn hàng',
                    ]
                ]
            ],
        ],
        2 => [
            'name' => 'Giao hàng',
            'detail' => [
//                21 => [
//                    'name' => 'Lấy hàng thành công',
//                    'color' => 'info',
//                    'next' => [
//                        22 => 'Nhập kho',
//                        71 => 'Thất lạc',
//                        72 => 'Hư hỏng',
//                    ]
//                ],
                22 => [
                    'name' => 'Nhập kho',
                    'color' => 'info',
                    'next' => [
                        23 => 'Điều phối giao hàng',
                        22 => 'Chuyển kho',
                        25 => 'Lưu kho',
//                        32 => 'Gán nhân viên hoàn',
//                        31 => 'Chuyển hoàn',
//                        34 => 'Chờ duyệt hoàn',
                        // 71 => 'Thất lạc',
                        // 72 => 'Hư hỏng',
                    ]
                ],
                23 => [
                    'name' => 'Đang giao hàng',
                    'color' => 'info',
                    'next' => [
                        // 41 => 'Chờ xác nhận giao lại',
//                        24 => 'Giao hàng không thành công',
//                        31 => 'Chuyển hoàn',
//                        34 => 'Chờ duyệt hoàn',
                        // 51 => 'Giao hàng thành công',
                        // 71 => 'Thất lạc',
//                        72 => 'Hư hỏng',
                    ]
                ],
//                24 => [
//                    'name' => 'Giao hàng không thành công',
//                    'color' => 'info',
//                    'next' => [
//                        25 => 'Lưu kho',
//                        34 => 'Chờ duyệt hoàn',
//                        41 => 'Chờ xác nhận giao lại',
//                    ]
//                ],
                25 => [
                    'name' => 'Lưu kho',
                    'color' => 'info',
                    'next' => [
                        23 => 'Điều phối giao hàng',
                        // 31 => 'Chuyển hoàn',
                        // 41 => 'Chờ xác nhận giao lại',
                    ]
                ]
            ]
        ],
        4 => [
            'name' => 'Chờ xác nhận giao lại',
            'detail' => [
                41 => [
                    'name' => 'Chờ xác nhận giao lại',
                    'color' => 'primary',
                    'next' => [
                        23 => 'Đang giao hàng',
                        22 => 'Nhập kho',
                        // 34 => 'Chờ duyệt hoàn',
                    ]
                ]
            ]
        ],
        3 => [
            'name' => 'Hoàn hàng',
            'detail' => [
                34 => [
                    'name' => 'Chờ duyệt hoàn',
                    'color' => 'warning',
                    'next' => [
                        23 => 'Đang giao hàng',
                        // 35 => 'Duyệt hoàn',
                    ]
                ],
                35 => [
                    'name' => 'Duyệt hoàn',
                    'color' => 'warning',
                    'next' => [
                        36 => 'Hoàn nhập kho',
                    ]
                ],
                31 => [
                    'name' => 'Chuyển hoàn',
                    'color' => 'info',
                    'next' => [
                        32 => 'Gán nhân viên hoàn',
                        // 41 => 'Chờ xác nhận giao lại',
                        // 71 => 'Thất lạc',
                        // 72 => 'Hư hỏng',
                    ]
                ],
                36 => [
                    'name' => 'Hoàn nhập kho',
                    'color' => 'info',
                    'next' => [
                        32 => 'Gán nhân viên hoàn',
                    ]
                ],
                32 => [
                    'name' => 'Đang hoàn hàng',
                    'color' => 'dark',
                    'next' => [
//                        33 => 'Hoàn hàng không thành công',
                        // 71 => 'Thất lạc',
                    ]
                ],
//                33 => [
//                    'name' => 'Hoàn hàng không thành công',
//                    'color' => 'dark',
//                    'next' => [
//                        25 => 'Lưu kho',
//                        52 => 'Hoàn hàng thành công',
//                    ]
//                ]
            ]
        ],
        5 => [
            'name' => 'Giao thành công',
            'detail' => [
                51 => [
                    'name' => 'Giao hàng thành công',
                    'color' => 'success',
                    'next' => []
                ],
//                52 => [
//                    'name' => 'Hoàn hàng thành công',
//                    'color' => 'success',
//                    'next' => [
//                        82 => 'Đã đối soát hoàn hàng',
//                    ]
//                ],
            ]
        ],
        6 => [
            'name' => 'Đơn huỷ',
            'detail' => [
                61 => [
                    'name' => 'Đơn huỷ',
                    'color' => 'danger',
                    'next' => []
                ],
            ]
        ],
        7 => [
            'name' => 'Thất lạc - hư hỏng',
            'detail' => [
                71 => [
                    'name' => 'Thất lạc',
                    'color' => 'danger',
                    'next' => [
                        // 73 => 'Đã thoả thuận thất lạc',
                    ]
                ],
                72 => [
                    'name' => 'Hư hỏng',
                    'color' => 'danger',
                    'next' => [
                        // 74 => 'Đã thoả thuận hư hỏng',
                    ]
                ],
                73 => [
                    'name' => 'Đã thoả thuận thất lạc',
                    'color' => 'success',
                    'next' => []
                ],
                74 => [
                    'name' => 'Đã thoả thuận hư hỏng',
                    'color' => 'success',
                    'next' => [
                        32 => 'Gán nhân viên hoàn',
                    ]
                ],
            ]
        ]
    ];

    const statusPushsale = [
        0 => [
            'name' => 'Chưa tiếp nhận',
            'detail' => []
        ],
        1 => [
            'name' => 'Chờ bàn giao',
            'detail' => [
                11 => [
                    'name' => 'Chờ lấy hàng',
                    'color' => 'warning',
                    'next' => []
                ],
                12 => [
                    'name' => 'Đang lấy hàng',
                    'color' => 'info',
                    'next' => []
                ],
                13 => [
                    'name' => 'Lấy hàng không thành công',
                    'color' => 'info',
                    'next' => []
                ]
            ],
        ],
        2 => [
            'name' => 'Giao hàng',
            'detail' => [
                22 => [
                    'name' => 'Nhập kho',
                    'color' => 'info',
                    'next' => []
                ],
                23 => [
                    'name' => 'Đang giao hàng',
                    'color' => 'info',
                    'next' => []
                ],
                25 => [
                    'name' => 'Lưu kho',
                    'color' => 'info',
                    'next' => []
                ]
            ]
        ],
        4 => [
            'name' => 'Chờ xác nhận giao lại',
            'detail' => [
                41 => [
                    'name' => 'Chờ xác nhận giao lại',
                    'color' => 'primary',
                    'next' => []
                ]
            ]
        ],
        3 => [
            'name' => 'Hoàn hàng',
            'detail' => [
                34 => [
                    'name' => 'Chờ duyệt hoàn',
                    'color' => 'warning',
                    'next' => []
                ],
                35 => [
                    'name' => 'Duyệt hoàn',
                    'color' => 'warning',
                    'next' => []
                ],
                31 => [
                    'name' => 'Chuyển hoàn',
                    'color' => 'info',
                    'next' => []
                ],
                36 => [
                    'name' => 'Hoàn nhập kho',
                    'color' => 'info',
                    'next' => []
                ],
                32 => [
                    'name' => 'Đang hoàn hàng',
                    'color' => 'dark',
                    'next' => []
                ],
            ]
        ],
        5 => [
            'name' => 'Giao thành công',
            'detail' => [
                51 => [
                    'name' => 'Giao hàng thành công',
                    'color' => 'success',
                    'next' => []
                ],
            ]
        ],
        6 => [
            'name' => 'Đơn huỷ',
            'detail' => [
                61 => [
                    'name' => 'Đơn huỷ',
                    'color' => 'danger',
                    'next' => []
                ],
            ]
        ],
        7 => [
            'name' => 'Thất lạc - hư hỏng',
            'detail' => [
                71 => [
                    'name' => 'Thất lạc',
                    'color' => 'danger',
                    'next' => []
                ],
                72 => [
                    'name' => 'Hư hỏng',
                    'color' => 'danger',
                    'next' => []
                ],
                73 => [
                    'name' => 'Đã thoả thuận thất lạc',
                    'color' => 'success',
                    'next' => []
                ],
                74 => [
                    'name' => 'Đã thoả thuận hư hỏng',
                    'color' => 'success',
                    'next' => []
                ],
            ]
        ]
    ];

//    const splitShipper = [
//        'shipper' => [2, 4, 5, 6, 7],
//        'pickup' => [1, 6, 7],
//        'refund' => [3, 5, 6, 7],
//    ];

    const splitShipperDetail = [
        'shipper' => [23, 41, 71, 34, 35, 36],
        'pickup' => [12, 13, 61],
        'refund' => [32, 71],
    ];

    const statusShipperMobile = [
        12 => [
            'name' => 'Đang lấy hàng',
        ],
        13 => [
            'name' => 'Lấy hàng không thành công',
        ],
        22 => [
            'name' => 'Lấy hàng thành công',
        ],
        23 => [
            'name' => 'Đang giao hàng',
        ],
        24 => [
            'name' => 'Giao hàng không thành công',
        ],
        32 => [
            'name' => 'Đang hoàn hàng',
        ],
        34 => [
            'name' => 'Chờ duyệt hoàn',
        ],
        35 => [
            'name' => 'Đang hoàn về kho',
        ],
        36 => [
            'name' => 'Hoàn nhập kho',
        ],
        41 => [
            'name' => 'Chờ xác nhận giao lại',
        ],
        51 => [
            'name' => 'Giao hàng thành công',
        ],
        61 => [
            'name' => 'Đơn huỷ',
        ],
        71 => [
            'name' => 'Thất lạc',
        ],
        72 => [
            'name' => 'Hư hỏng',
        ],
        82 => [
            'name' => 'Đối soát hoàn hàng',
        ],
    ];

//    const statusShipper = [
//        1 => [
//            'name' => 'Chờ bàn giao',
//            'detail' => [
//                12 => [
//                    'name' => 'Đang lấy hàng',
//                    'color' => 'info',
//                    'next' => [
//                        13 => 'Lấy hàng không thành công',
//                        61 => 'Huỷ đơn hàng',
//                    ]
//                ],
//                13 => [
//                    'name' => 'Lấy hàng không thành công',
//                    'color' => 'info',
//                    'next' => [
//                        12 => 'Đang lấy hàng',
//                        61 => 'Huỷ đơn hàng',
//                    ]
//                ]
//            ],
//        ],
//        2 => [
//            'name' => 'Giao hàng',
//            'detail' => [
//                22 => [
//                    'name' => 'Lấy hàng thành công',
//                    'color' => 'info',
//                    'next' => []
//                ],
//                23 => [
//                    'name' => 'Đang giao hàng',
//                    'color' => 'info',
//                    'next' => [
//                        41 => 'Chờ xác nhận giao lại',
//                        51 => 'Giao hàng thành công',
//                        71 => 'Thất lạc',
//                        72 => 'Hư hỏng',
//                    ]
//                ],
//            ]
//        ],
//        3 => [
//            'name' => 'Hoàn hàng',
//            'detail' => [
//                34 => [
//                    'name' => 'Chờ duyệt hoàn',
//                    'color' => 'warning',
//                    'next' => [
//                        23 => 'Đang giao hàng',
//                        35 => 'Duyệt hoàn',
//                    ]
//                ],
//                32 => [
//                    'name' => 'Đang hoàn hàng',
//                    'color' => 'dark',
//                    'next' => [
////                        33 => 'Hoàn hàng không thành công',
//                        82 => 'Đã đối soát hoàn hàng',
//                        71 => 'Thất lạc',
//                    ]
//                ],
//            ]
//        ],
//        4 => [
//            'name' => 'Chờ xác nhận giao lại',
//            'detail' => []
//        ],
//        5 => [
//            'name' => 'Hoàn tất',
//            'detail' => [
//                51 => [
//                    'name' => 'Giao hàng thành công',
//                    'color' => 'success',
//                    'next' => [
//                    ]
//                ],
//            ]
//        ],
//        6 => [
//            'name' => 'Đơn huỷ',
//            'detail' => [
//                61 => [
//                    'name' => 'Đơn huỷ',
//                    'color' => 'danger',
//                    'next' => [
//                    ]
//                ],
//            ]
//        ],
//        7 => [
//            'name' => 'Thất lạc - hư hỏng',
//            'detail' => [
//                71 => [
//                    'name' => 'Thất lạc',
//                    'color' => 'danger',
//                    'next' => [
//                    ]
//                ],
//                72 => [
//                    'name' => 'Hư hỏng',
//                    'color' => 'danger',
//                    'next' => [
//                    ]
//                ],
//            ]
//        ]
//    ];

    const statusShipperPickup = [
        1 => [
            'name' => 'Chờ bàn giao',
            'detail' => [
                12 => [
                    'name' => 'Đang lấy hàng',
                    'color' => 'info',
                    'next' => [
                        13 => 'Lấy hàng không thành công',
                        61 => 'Huỷ đơn hàng',
                    ]
                ],
                13 => [
                    'name' => 'Lấy hàng không thành công',
                    'color' => 'info',
                    'next' => [
                        12 => 'Đang lấy hàng',
                        61 => 'Huỷ đơn hàng',
                    ]
                ]
            ],
        ],
        2 => [
            'name' => 'Lấy hàng thành công',
            'detail' => [
//                21 => [
//                    'name' => 'Lấy hàng thành công',
//                    'color' => 'info',
//                    'next' => [
//                        22 => 'Nhập kho',
//                        71 => 'Thất lạc',
//                        72 => 'Hư hỏng',
//                    ]
//                ],
                22 => [
                    'name' => 'Lấy hàng thành công',
                    'color' => 'info',
                    'next' => []
                ]
            ]
        ],
//        3 => [
//            'name' => 'Hoàn hàng',
//            'detail' => [
//                34 => [
//                    'name' => 'Chờ duyệt hoàn',
//                    'color' => 'warning',
//                    'next' => [
//                        23 => 'Đang giao hàng',
//                        35 => 'Duyệt hoàn',
//                    ]
//                ],
//                32 => [
//                    'name' => 'Đang hoàn hàng',
//                    'color' => 'dark',
//                    'next' => [
//                        82 => 'Đã đối soát hoàn hàng',
//                        71 => 'Thất lạc',
//                    ]
//                ],
//            ]
//        ],
        6 => [
            'name' => 'Đơn huỷ',
            'detail' => [
                61 => [
                    'name' => 'Đơn huỷ',
                    'color' => 'danger',
                    'next' => [
                    ]
                ],
            ]
        ],
//        7 => [
//            'name' => 'Thất lạc - hư hỏng',
//            'detail' => [
//                71 => [
//                    'name' => 'Thất lạc',
//                    'color' => 'danger',
//                    'next' => [
//                    ]
//                ],
//                72 => [
//                    'name' => 'Hư hỏng',
//                    'color' => 'danger',
//                    'next' => [
//                    ]
//                ],
//            ]
//        ]
    ];

    const statusShipperShip = [
        2 => [
            'name' => 'Giao hàng',
            'detail' => [
                23 => [
                    'name' => 'Đang giao hàng',
                    'color' => 'info',
                    'next' => [
                        41 => 'Chờ xác nhận giao lại',
                        51 => 'Giao hàng thành công',
                        71 => 'Thất lạc',
                        // 72 => 'Hư hỏng',
                    ]
                ],
            ]
        ],
        4 => [
            'name' => 'Chờ xác nhận giao lại',
            'detail' => [
                41 => [
                    'name' => 'Chờ xác nhận giao lại',
                    'color' => 'primary',
                    'next' => [
                        23 => 'Đang giao hàng',
                        51 => 'Giao hàng thành công'
                    ]
                ]
            ]
        ],
        5 => [
            'name' => 'Giao hàng thành công',
            'detail' => [
                51 => [
                    'name' => 'Giao hàng thành công',
                    'color' => 'success',
                    'next' => []
                ],
            ]
        ],
        3 => [
            'name' => 'Hoàn hàng',
            'detail' => [
                34 => [
                    'name' => 'Chờ duyệt hoàn',
                    'color' => 'warning',
                    'next' => []
                ],
                35 => [
                    'name' => 'Duyệt hoàn',
                    'color' => 'warning',
                    'next' => []
                ],
                36 => [
                    'name' => 'Hoàn nhập kho',
                    'color' => 'info',
                    'next' => []
                ],
            ]
        ],
        7 => [
            'name' => 'Thất lạc',
            'detail' => [
                71 => [
                    'name' => 'Thất lạc',
                    'color' => 'danger',
                    'next' => []
                ],
//                72 => [
//                    'name' => 'Hư hỏng',
//                    'color' => 'danger',
//                    'next' => []
//                ],
            ]
        ]
    ];

    const statusShipperRefund = [
        3 => [
            'name' => 'Hoàn hàng',
            'detail' => [
                32 => [
                    'name' => 'Đang hoàn hàng',
                    'color' => 'dark',
                    'next' => [
                        // 82 => 'Đã đối soát hoàn hàng',
                        71 => 'Thất lạc',
                    ]
                ],
            ]
        ],
        8 => [
            'name' => 'Đối soát hoàn hàng',
            'detail' => [
                82 => [
                    'name' => 'Đã đối soát hoàn hàng',
                    'color' => 'success',
                    'next' => []
                ],
            ]
        ],
        7 => [
            'name' => 'Thất lạc',
            'detail' => [
                71 => [
                    'name' => 'Thất lạc',
                    'color' => 'danger',
                    'next' => []
                ]
            ]
        ]
    ];

    const status_last_change = [11, 21, 23, 24, 41, 51, 52, 71, 72];

    const status_queue = [
        0 => [
            'name' => 'Chờ xử lý',
            'color' => 'warning'
        ],
        1 => [
            'name' => 'Thành công',
            'color' => 'success'
        ],
        2 => [
            'name' => 'Thất bại',
            'color' => 'danger'
        ]
    ];

    const breadcrumb = array(
        "Home" => "Trang chủ",
        "menu" => "Điều hướng",
        "roles" => "Vai trò",
        "element" => "Đường dẫn",
        "account" => "Tài khoản",
        "admin" => "Quản trị",
        "users" => "Nhân viên",
        "shops" => "Cửa hàng",
        "orders" => "Đơn hàng",
        "contacts" => "Hỗ trợ",
        "order-service" => "Gói cước",
        "cod" => "Phí thu hộ",
        "order-setting" => "Biểu phí",
        "fee-pick" => "Phí lấy hàng",
        "fee-insurance" => "Phí bảo hiểm",
        "calculator-fee" => "Tính phí vận chuyển",
        "contacts-type" => "Loại hỗ trọ",
        "post-offices" => "Bưu cục",
        "provinces" => "Tỉnh thành",
        "districts" => "Quận huyện",
        "wards" => "Phường xã",
        "cash-flow" => "Dòng tiền",
        "order-incurred-fee" => "Chi phí phát sinh",
        "reconcile-history" => "Lịch sử đối soát",
        "print-templates" => "Mẫu in",
        "mail" => "Mẫu thư điện tủ",
        "report" => "Báo cáo",
        "reports" => "Báo cáo",
        "by-zone" => "Báo cáo vùng",
        "by-ship" => "Báo cáo ship",
        "cod-report" => "Báo cáo tiền thu hộ",
        "by-status" => "Báo cáo trạng thái",
        "shop-notification" => "Thông báo",
        "create" => "Thêm mới",
        "edit" => "Cập nhật",
    );

    const incurred_fee_by_status = [
        'incurred_total_cod' => [
            22, 23, 25, 34, 41, 51, 91
        ],
        'incurred_money_indemnify' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ],
        'incurred_fee_transport' => [
            22, 23, 41, 31, 32, 34, 35, 36, 51, 91
        ],
        'incurred_fee_insurance' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ],
        'incurred_fee_cod' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ],
        'incurred_fee_refund' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ],
        'incurred_fee_store' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ],
        'incurred_fee_change_info' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ],
        'incurred_fee_transfer' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ],
        'incurred_fee_pick' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ],
        'incurred_fee_forward' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ],
        'incurred_refund_cod' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ],
        'incurred_refund_transport' => [
            11, 12, 13, 22, 23, 25, 31, 32, 34, 35, 36, 41, 51, 61, 71, 72, 73, 74, 81, 82, 83, 84, 91
        ]
    ];

    const statusShopApi = [1, 2, 4, 3, 5, 8, 6, 7];
}
