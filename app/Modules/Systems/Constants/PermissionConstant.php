<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Constants;

/**
 * Class PermissionConstant
 * @package App\Modules\Orders\Constants
 * @author HuyDien <huydien.it@gmail.com>
 */
class PermissionConstant
{
    const roles = [
        'superadmin' => [
            'name' => 'Quản trị cấp cao',
            'color' => 'danger'
        ],
        'admin' => [
            'name' => 'Quản trị hệ thống',
            'color' => 'danger'
        ],
        'accountancy' => [
            'name' => 'Kế toán',
            'color' => 'warning'
        ],
        'coordinator' => [
            'name' => 'Điều phối',
            'color' => 'success'
        ],
        'pushsale' => [
            'name' => 'Giục đơn',
            'color' => 'info'
        ],
        'support' => [
            'name' => 'Chăm sóc khách hàng',
            'color' => 'primary'
        ],
        'shipper' => [
            'name' => 'Nhân viên giao hàng',
            'color' => 'dark'
        ],
        'pickup' => [
            'name' => 'Nhân viên lấy hàng',
            'color' => 'dark'
        ],
        'refund' => [
            'name' => 'Nhân viên hoàn hàng',
            'color' => 'dark'
        ]
    ];

    const rolesShop = [
        'shop' => [
            'name' => 'Chủ shop',
            'color' => 'danger'
        ],
        'shop_pushsale' => [
            'name' => 'Giục đơn',
            'color' => 'info'
        ]
    ];
    const permissions = [
        'users' => [
            'name' => 'Nhân viên',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin']
            ],
            'delete' => [
                'name' => 'Xoá',
                'default' => ['admin']
            ],
        ],
        'shops' => [
            'name' => 'Quản lý Shop',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            'delete' => [
                'name' => 'Xoá',
                'default' => ['admin', 'accountancy']
            ],
            'cskh' => [
                'name' => 'Chăm sóc khách hàng',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'orders' => [
            'name' => 'Quản lý đơn hàng',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy', 'coordinator', 'pushsale', 'support', 'shipper', 'pickup']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy', 'coordinator', 'pushsale', 'support', 'shipper', 'pickup']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy', 'coordinator', 'pushsale', 'support', 'shipper', 'pickup']
            ],
            'assign_ship' => [
                'name' => 'Gán ship',
                'default' => ['admin', 'accountancy', 'coordinator']
            ],
            'export' => [
                'name' => 'Xuất excel',
                'default' => ['admin', 'accountancy']
            ],
            'import' => [
                'name' => 'Lên đơn excel',
                'default' => ['admin', 'accountancy']
            ],
            'print' => [
                'name' => 'In đơn',
                'default' => ['admin', 'accountancy']
            ],
            'delete' => [
                'name' => 'Xoá',
                'default' => ['admin', 'accountancy', 'coordinator', 'pushsale', 'support', 'shipper', 'pickup']
            ],
        ],
        'contacts' => [
            'name' => 'Quản lý hỗ trợ',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy', 'coordinator', 'pushsale', 'support', 'shipper', 'pickup']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy', 'coordinator', 'pushsale', 'support', 'shipper', 'pickup']
            ],
            'assign' => [
                'name' => 'Gán người xử lý',
                'default' => ['admin', 'coordinator']
            ],
            'handler' => [
                'name' => 'Xử lý',
                'default' => ['pushsale', 'support']
            ],
            'delete' => [
                'name' => 'Xoá',
                'default' => ['admin', 'accountancy', 'coordinator', 'pushsale', 'support', 'shipper', 'pickup']
            ],
        ],
        'contacts_type' => [
            'name' => 'Quản lý loại hỗ trợ',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            'delete' => [
                'name' => 'Xoá',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'order_services' => [
            'name' => 'Quản lý gói cước',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            'delete' => [
                'name' => 'Xoá',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'order_settings_fee' => [
            'name' => 'Cài đặt biểu phí',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'order_settings_cod' => [
            'name' => 'Cài đặt phí thu hộ',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            'delete' => [
                'name' => 'Xoá',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'order_settings_pick' => [
            'name' => 'Cài đặt phí lấy hàng',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            'delete' => [
                'name' => 'Xoá',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'order_settings_insurance' => [
            'name' => 'Cài đặt phí bảo hiểm',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            'delete' => [
                'name' => 'Xoá',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'order_calculator_fee' => [
            'name' => 'Tính phí vận chuyển',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'post_offices' => [
            'name' => 'Quản lý bưu cục',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            // 'delete' => [
            //     'name' => 'Xoá',
            //     'default' => ['admin', 'accountancy']
            // ],
        ],
        'provinces' => [
            'name' => 'Quản lý Tỉnh/Thành',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            // 'delete' => [
            //     'name' => 'Xoá',
            //     'default' => ['admin', 'accountancy']
            // ],
        ],
        'order_fee' => [
            'name' => 'Tiền/Phí phát sinh',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            'delete' => [
                'name' => 'Xoá',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'districts' => [
            'name' => 'Quản lý Quận/Huyện',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            // 'delete' => [
            //     'name' => 'Xoá',
            //     'default' => ['admin', 'accountancy']
            // ],
        ],
        'wards' => [
            'name' => 'Quản lý Phường/Xã',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
            // 'delete' => [
            //     'name' => 'Xoá',
            //     'default' => ['admin', 'accountancy']
            // ],
        ],
        'cash_flow' => [
            'name' => 'Quản lý dòng tiền',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'check' => [
                'name' => 'Đối soát',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'print_template' => [
            'name' => 'Quản lý mẫu in',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'update' => [
                'name' => 'Sửa',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'report_by_zone' => [
            'name' => 'Theo dõi báo cáo khu vực',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'report_by_ship' => [
            'name' => 'Theo dõi báo cáo ship',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'report_by_cod' => [
            'name' => 'Theo dõi báo cáo tiền thu hộ',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'report_by_status' => [
            'name' => 'Theo dõi báo cáo trạng thái',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'reconcile_history' => [
            'name' => 'Lịch sử đối soát',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
        ],
        'shop_notification' => [
            'name' => 'Thông báo',
            'view' => [
                'name' => 'Xem',
                'default' => ['admin', 'accountancy']
            ],
            'create' => [
                'name' => 'Thêm',
                'default' => ['admin', 'accountancy']
            ],
//            'update' => [
//                'name' => 'Sửa',
//                'default' => ['admin', 'accountancy']
//            ],
//             'delete' => [
//                 'name' => 'Xoá',
//                 'default' => ['admin', 'accountancy']
//             ],
        ],
    ];
}
