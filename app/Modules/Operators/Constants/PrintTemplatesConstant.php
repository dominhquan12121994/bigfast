<?php

namespace App\Modules\Operators\Constants;

class PrintTemplatesConstant
{
    const size = [
        'A4' => [
            'doc'   => [
                'name'      => 'A4 dọc',
                'width'     => '210',
                'height'    => '297',
            ],
            'ngang'   => [
                'name'      => 'A4 ngang',
                'width'     => '297',
                'height'    => '210',
            ],
        ],
        'A5' => [
            'doc'   => [
                'name'      => 'A5 dọc',
                'width'     => '148',
                'height'    => '210',
            ],
            'ngang'   => [
                'name'      => 'A5 ngang',
                'width'     => '210',
                'height'    => '148',
            ],
        ],
        'A6' => [
            'doc'   => [
                'name'      => 'A6 dọc',
                'width'     => '105',
                'height'    => '148',
            ],
            'ngang'   => [
                'name'      => 'A6 ngang',
                'width'     => '148',
                'height'    => '105',
            ],
        ],
        'A7' => [
            'doc'   => [
                'name'      => 'A7 dọc',
                'width'     => '74',
                'height'    => '105',
            ],
            'ngang'   => [
                'name'      => 'A7 ngang',
                'width'     => '105',
                'height'    => '74',
            ],
        ],
        'K80' => [
            'doc'   => [
                'name'      => 'K80',
                'width'     => '80',
                'height'    => '80',
            ]
        ]
    ];

    const per_page = [
        'A7' => [
            1 => [
                'doc' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'A7',
                    'xoay' => false
                ],
                'ngang' => [
                    'kho_giay' => 'ngang',
                    'mau_in' => 'A7',
                    'xoay' => true
                ],
            ],
        ],
        'A6' => [
            1 => [
                'doc' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'A6',
                    'xoay' => false
                ],
                'ngang' => [
                    'kho_giay' => 'ngang',
                    'mau_in' => 'A6',
                    'xoay' => true
                ],
            ],
            2 => [
                'doc' => [
                    'kho_giay' => 'ngang',
                    'mau_in' => 'A7',
                    'xoay' => true
                ],
                'ngang' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'A7',
                    'xoay' => false
                ],
            ],
        ],
        'A5' => [
            1 => [
                'doc' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'A5',
                    'xoay' => false
                ],
                'ngang' => [
                    'kho_giay' => 'ngang',
                    'mau_in' => 'A5',
                    'xoay' => true
                ],
            ],
            2 => [
                'doc' => [
                    'kho_giay' => 'ngang',
                    'mau_in' => 'A6',
                    'xoay' => true
                ],
                'ngang' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'A6',
                    'xoay' => false
                ],
            ],
            4 => [
                'doc' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'A7',
                    'xoay' => false
                ],
                'ngang' => [
                    'kho_giay' => 'ngang',
                    'mau_in' => 'A7',
                    'xoay' => true
                ],
            ],
        ],
        'A4' => [
            1 => [
                'doc' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'A4',
                    'xoay' => false
                ],
                'ngang' => [
                    'kho_giay' => 'ngang',
                    'mau_in' => 'A4',
                    'xoay' => true
                ],
            ],
            2 => [
                'doc' => [
                    'kho_giay' => 'ngang',
                    'mau_in' => 'A5',
                    'xoay' => true
                ],
                'ngang' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'A5',
                    'xoay' => false
                ],
            ],
            4 => [
                'doc' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'A6',
                    'xoay' => false
                ],
                'ngang' => [
                    'kho_giay' => 'ngang',
                    'mau_in' => 'A6',
                    'xoay' => true
                ],
            ],
            8 => [
                'doc' => [
                    'kho_giay' => 'ngang',
                    'mau_in' => 'A7',
                    'xoay' => true
                ],
                'ngang' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'A7',
                    'xoay' => false
                ],
            ],
        ],
        'K80' => [
            1 => [
                'doc' => [
                    'kho_giay' => 'doc',
                    'mau_in' => 'K80',
                    'xoay' => false
                ]
            ]
        ]
    ];

    const key_word = [
        'cuahang' => [
            'alias' => 'shop',
            'name' => 'Cửa hàng',
            'lists' => [
                '{__LOGO_DOANH_NGHIEP__}' => [
                    'name' => 'Logo doanh nghiệp',
                    'default' => "<img src='/assets/logo/logo-new-268x122.png' style='max-width:194px'>",
                ],
                '{__LOGO_DOANH_NGHIEP_SMALL__}' => [
                    'name' => 'Logo doanh nghiệp nhỏ',
                    'default' => "<img src='/assets/logo/logo-new-268x122.png' style='max-width:120px'>",
                ],
                '{__SDT_CUA_HANG__}' => [
                    'name' => 'SĐT Cửa hàng',
                    'default' => '0978213661',
                    'alias' => 'phone',
                ],
                '{__EMAIL_CUA_HANG__}' => [
                    'name' => 'Email Cửa hàng',
                    'default' => 'bigfast.gmail.com.vn',
                    'alias' => 'email',
                ],
                '{__DIA_CHI_CUA_HANG__}' => [
                    'name' => 'Địa chỉ Cửa hàng',
                    'default' => 'R4 Royal City',
                    'alias' => 'address',
                ],
                '{__TEN_CUA_HANG__}' => [
                    'name' => 'Tên cửa hàng',
                    'default' => 'Big Fast',
                    'alias' => 'name',
                ],
            ]
        ],
        'donhang' => [
            'alias' => 'info',
            'name' => 'Đơn hàng',
            'lists' => [
                '{__MA_DH__}' => [
                    'name' => 'Mã đơn hàng',
                    'default' => 'B21052199994',
                    'alias' => 'lading_code',
                ],
                '{__MA_VACH_DH__}' => [
                    'name' => 'Mã vạch đơn hàng',
                    'default' => 'B21052199994',
                    'barcode' => 'short',
                    'alias' => 'lading_code',
                ],
                '{__MA_VACH_DH_SIZE_LARGE__}' => [
                    'name' => 'Mã vạch đơn hàng lớn',
                    'default' => 'B21052199994',
                    'barcode' => 'long',
                    'alias' => 'lading_code',
                ],
                '{__GOI_CUOC__}' => [
                    'name' => 'Gói cước',
                    'default' => 'Giao hàng nhanh',
                    'alias' => 'service_type',
                ],
                '{__NGAY_HIEN_TAI__}' => [
                    'name' => 'Ngày hiện tại',
                    'default' => '20-10-2020',
                ],
                '{__NGAY_LAY_HANG_DU_KIEN__}' => [
                    'name' => 'Ngày lấy hàng dự kiến',
                    'default' => '07-03-2021 12:00',
                ],
                '{__PHI_VAN_CHUYEN__}' => [
                    'name' => 'Phí vận chuyển',
                    'default' => 20000,
                    'alias' => 'transport_fee',
                ],
                '{__PHU_THU__}' => [
                    'name' => 'Phụ thu',
                    'default' => 20000,
                    'alias' => 'cod',
                ],
                '{__TONG_THU__}' => [
                    'name' => 'Tổng thu',
                    'default' => 20000,
                ],
                '{__LUU_Y__}' => [
                    'name' => 'Lưu ý',
                    'default' => 'Cho xem hàng cho thử',
                ],
                '{__GHI_CHU__}' => [
                    'name' => 'Ghi chú',
                    'default' => 'Hàng giao vào đêm',
                ],
                '{__CLIENT_CODE__}' => [
                    'name' => 'Client code',
                    'default' => '123048334',
                ],
            ]
        ],
        'sanpham' => [
            'alias' => 'products',
            'name' => 'Sản phẩm',
            'lists' => [
                '{__DANH_SACH_SP__}' => [
                    'name' => 'Danh sách sản phẩm',
                    'default' => 'Danh sách 1, Danh sách 2, ...',
                ],
                '{__STT_SP__}' => [
                    'name' => 'STT',
                    'default' => 1,
                ],
                '{__MA_SP__}' => [
                    'name' => 'Mã sản phẩm',
                    'default' => 'BF12314',
                ],
                '{__KHOI_LUONG_SP__}' => [
                    'name' => 'Khối lượng sản phẩm',
                    'default' => 20,
                ],
                '{__SL_SP__}' => [
                    'name' => 'Số lượng sản phẩm',
                    'default' => 20,
                ],
                '{__TONG_TIEN_SP__}' => [
                    'name' => 'Tổng tiền sản phẩm',
                    'default' => 20000,
                ],
            ]
        ],
        'nguoinhan' => [
            'alias' => 'receiver',
            'name' => 'Người nhận',
            'lists' => [
                '{__TEN_NGUOI_NHAN__}' => [
                    'name' => 'Tên người nhận',
                    'default' => 'Huyền',
                    'alias' => 'name',
                ],
                '{__SDT_NGUOI_NHAN__}' => [
                    'name' => 'SĐT người nhận',
                    'default' => '070986785',
                    'alias' => 'phone',
                ],
                '{__DIA_CHI_NGUOI_NHAN__}' => [
                    'name' => 'Địa chỉ người nhận',
                    'default' => 'Long Biên',
                    'alias' => 'address',
                ],
                '{__PHUONG_XA_NGUOI_NHAN__}' => [
                    'name' => 'Phường xã người nhận',
                    'default' => 'Bồ Đề',
                    'alias' => 'w_name',
                ],
                '{__QUAN_HUYEN_NGUOI_NHAN__}' => [
                    'name' => 'Quận huyện người nhận',
                    'default' => 'Long Biên',
                    'alias' => 'd_name',
                ],
                '{__TINH_THANH_NGUOI_NHAN__}' => [
                    'name' => 'Tỉnh thành người nhận',
                    'default' => 'Long Biên',
                    'alias' => 'p_name',
                ],
            ]
        ],
    ];
}
