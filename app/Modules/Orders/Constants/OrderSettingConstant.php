<?php

namespace App\Modules\Orders\Constants;

/**
 * Class OrderSettingConstant
 * @package App\Modules\Orders\Constants
 * @author HuyDien <huydien.it@gmail.com>
 */
class OrderSettingConstant
{
    const route = [
        'noitinh' => 'Nội Tỉnh',
        'noivung' => 'Nội Vùng',
        'noivungtinh' => 'Nội Vùng Tỉnh',
        'lienvungdacbiet' => 'Liên Vùng Đặc Biệt',
        'lienvung' => 'Liên Vùng',
        'lienvungtinh' => 'Liên Vùng Tỉnh'
    ];

    const color = [
        'noitinh' => 'badge-primary',
        'noivung' => 'badge-secondary',
        'noivungtinh' => 'badge-success',
        'lienvungdacbiet' => 'badge-danger',
        'lienvung' => 'badge-warning',
        'lienvungtinh' => 'badge-info'
    ];

    const lienvungdacbiet = [
        1 => 'Thành phố Hà Nội',
        48 => 'Thành phố Hồ Chí Minh',
        79 => 'Thành phố Đà Nẵng',
    ];

    const region = [
        'noi' => 'Nội thành',
        'ngoai' => 'Ngoại thành',
        'huyen' => 'Huyện/Xã'
    ];

    const fee_type = [
        // 'fee_send' => 'Phí vận chuyển',
        // 'fee_pick' => 'Phí lấy hàng',
        // 'fee_forward' => 'Phí chuyển tiêp',
        'fee_transport' => 'Phí vận chuyển',
    ];

    const extra = 0.5;
}