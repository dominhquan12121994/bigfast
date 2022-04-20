<?php

namespace App\Modules\Operators\Constants;

class ContactsConstant
{
    const constant = [
        'edit' => [
            'name' => 'Yêu cầu thay đổi thông tin',
            'children' => [
                'edit_money' => 'Sửa tiền đơn hàng',
                'edit_address' => 'Sửa địa chỉ giao hàng',
                'edit_phonenumber' => 'Sửa số điện thoại giao hàng',
                'edit_weight' => 'Sửa khối lượng đơn hàng',
                'cancel_order' => 'Hủy đơn hàng',
            ]
        ],
        'appointment' => [
            'name' => 'Hẹn lịch',
            'children' => [
                'appointment_deliver' => 'Hẹn giao hàng',
                'appointment_save' => 'Hẹn thời gian lưu kho',
            ]
        ],
        'urge' => [
            'name' => 'Giục lấy/giao/trả hàng',
            'children' => [
                'urge_take' => 'Giục lấy hàng',
                'urge_deliver' => 'Giục giao hàng',
                'urge_return' => 'Giục trả hàng',
                'urge_cod' => 'Giục chuyển tiền CoD'
            ]
        ],
        'complain' => [
            'name' => 'Khiếu nại',
            'children' => [
                'complain_info' => 'Cập nhật sai trạng thái ĐH',
                'complain_delay' => 'Cập nhật sai lý do Deloy',
                'complain_payment' => 'Chưa nhận được tiền chuyển khoản',
                'complain_control' => 'Đối soát thiếu tiền',
                'complain_money' => 'BigFast thu sai tiền thu hộ',
                'complain_broken' => 'Hàng hỏng vỡ',
                'complain_missing' => 'Hàng thất lạc, giao thiếu, giao nhầm',
                'complain_return' => 'Shop chưa nhận được hàng giả',
                'complain_behave' => 'Thái độ nhân viên BigFast'
            ]
        ],
        'another' => [
            'name' => 'Khác',
        ],
    ];

    const constant_parent = [
        0 => 'Yêu cầu thay đổi thông tin',
        1 => 'Hẹn lịch',
        2 => 'Giục lấy/giao/trả hàng',
        3 => 'Khiếu nại',
        4 => 'Khác',
    ];

    const level = [
        0 => 'Thấp',
        1 => 'Cao'
    ];
    const status = [
        0 => 'Chưa xử lý', 
        1 => 'Đang xử lý', 
        2 => 'Đã xử lý',
        3 => 'Từ chối'
    ];
}