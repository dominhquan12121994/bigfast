<?php

namespace App\Modules\Systems\Constants;

/**
 * Class NotificationConstant
 * @package App\Modules\Orders\Constants
 * @author HuyDien <huydien.it@gmail.com>
 */
class NotificationConstant
{
    const type = [
        0 => [
            'name' => 'Thông tin',
            'icon' => 'info-circle'
        ],
        1 => [
            'name' => 'Thành công',
            'icon' => 'check'
        ],
        2 => [
            'name' => 'Hủy',
            'icon' => 'exclamation-triangle'
        ]
    ];

    const content_pattern = [
        0 => '',
        1 => 'Bạn đã được gán ? đơn lấy hàng mới',
        2 => 'Bạn đã được gán ? đơn giao hàng mới',
        3 => 'Đơn hàng ? đã được giao thành công',
        4 => 'Đơn hàng ? đã bị hủy',
        5 => 'Bạn có ? đơn hàng đã ở trạng thái ? quá hạn',
        6 => 'Đơn hàng ? đã thay đổi thông tin',
        7 => 'Bạn đã được gán ? đơn hoàn hàng mới',
    ];
}
