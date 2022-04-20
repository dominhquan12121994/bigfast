<?php

namespace App\Modules\Orders\Constants;

/**
 * Class ShopConstant
 * @package App\Modules\Orders\Constants
 * @author HuyDien <huydien.it@gmail.com>
 */
class ShopConstant
{
    const bank = [
        'cycle_cod' => [
            'friday' => [
                'name' => 'Đối soát 1 lần/tuần (thứ 6)', // 7
                'days' => 7
            ],
            'tuesday_friday' => [
                'name' => 'Đối soát 2 lần/tuần thứ 3/6', // 3
                'days' => 3
            ],
            'monday_wednesday_friday' => [
                'name' => 'Đối soát 3 lần/tuần vào thứ 2/4/6', // 2
                'days' => 2
            ],
            'once_per_month' => [
                'name' => 'Đối soát 1 lần/tháng vào ngày cuối cùng của tháng', // 30
                'days' => 30
            ],
            'twice_per_month' => [
                'name' => 'Đối soát 2 lần/tháng vào ngày 15 và ngày cuối cùng của tháng', // 15
                'days' => 15
            ]
        ]
    ];

    const branchs = [
        'fashion' => [
            'name' => 'Thời trang',
        ],
        'cosmetic' => [
            'name' => 'Mỹ phẩm',
        ],
        'sport' => [
            'name' => 'Thể thao & dã ngoại',
        ],
        'fashion_accessories' => [
            'name' => 'Trang sức và phụ kiện thời trang',
        ],
        'electronic_equipment' => [
            'name' => 'Điện thoại và thiết bị điện tử',
        ],
        'electronic_accessories' => [
            'name' => 'Phụ kiện điện thoại, laptop và điện tử',
        ],
        'computer_laptop' => [
            'name' => 'Máy tính, laptop',
        ],
        'household_electrical' => [
            'name' => 'Tivi và thiết bị điện gia dụng',
        ],
        'furniture' => [
            'name' => 'Nội thất',
        ],
        'home_appliance' => [
            'name' => 'Đồ gia dụng, nhà cửa & đời sống',
        ],
        'transport_mean' => [
            'name' => 'Xe máy và phương tiện giao thông',
        ],
        'fragile_product' => [
            'name' => 'Hàng hoá dễ vỡ',
        ],
        'plant' => [
            'name' => 'Cây trồng & Chăm sóc cây trồng',
        ],
        'package_food' => [
            'name' => 'Thực phẩm, nông sản, hải sản có đóng gói',
        ],
        'consumer' => [
            'name' => 'Hàng tiêu dùng và tạp hoá',
        ],
        'book' => [
            'name' => 'Sách & Văn phòng phẩm',
        ],
        'other' => [
            'name' => 'Khác',
        ],
    ];

    const scales = [
        '0' => 'Không có nhu cầu thường xuyên',
        '150' => 'Dưới 150 ĐH/tháng',
        '900' => 'Từ 150 - 900 ĐH/tháng',
        '3000' => 'Từ 900 - 3000 ĐH/tháng',
        '6000' => 'Từ 3000 - 6000 ĐH/tháng',
        '10000' => 'Từ 6000 ĐH/tháng trở lên',
        '-1' => 'Không xác định',
    ];

    const purposes = [
        'personal' => 'Cá nhân',
        'enterprise' => 'Cửa hàng/Doanh nghiệp',
        'unknown' => 'Không xác định',
    ];
}
