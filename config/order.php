<?php
/**
 * Copyright (c) 2020. Electric
 */

return [
    'status' => [
        'init' => 0, // shop tao don
        'init_detail' => 0, // shop tao don
        'default' => 1, // admin tao don
        'default_detail' => 11, // trạng thái chi tiết
    ],
    'prefix_lading_code' => 'B',
    'status_over_time' => [
        2 => [ //lưu kho quá hạn 7 ngày
            'status_detail' => 25,
            'over_day' => 7,
            'text' => 'lưu kho',
        ],
        3 => [ //chờ chuyển hoàn quá hạn 5 ngày
            'status_detail' => 34,
            'over_day' => 5,
            'text' => 'chờ chuyển hoàn',
        ],
    ]
];
