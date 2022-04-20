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
class SystemLogConstant
{
    const log_name = [
        'orders' => 'Đơn hàng',
        'print_template' => 'Mẫu in',
        'contacts' => 'Yêu cầu hỗ trợ',
        'contacts_type' => 'Loại yêu cầu hỗ trợ',
        'districts' => 'Quận huyện',
        'post_offices' => 'Bưu cục',
        'provinces' => 'Tỉnh thành',
        'wards' => 'Phường xã',
        'settings_cod' => 'Cấu hình tiền Cod',
        'order_fee' => 'Tiền phát sinh',
        'settings_insurance' => 'Cấu hình tiền bảo hiểm',
        'settings_pick' => 'Cấu hình tiền lấy hàng',
        'order_services' => 'Gói cước',
        'shops' => 'Shop',
        'shop_staff' => 'Nhân viên shop',
        'cash_flow' => 'Đối soát',
        'setting_fee' => 'Biểu phí',
        'users' => 'Quản trị viên'
    ];

    const description = [
        'contacts_create' => 'Tạo mới yêu cầu hỗ trợ',
        'contacts_update' => 'Cập nhật yêu cầu hỗ trợ',
        'contacts_delete' => 'Xóa yêu cầu hỗ trợ',
        'contacts_refuse' => 'Từ chối yêu cầu hỗ trợ',
        'contacts_update' => 'Xóa loại yêu cầu hỗ trợ',
        'districts_create' => 'Tạo mới quận huyện',
        'districts_update' => 'Cập nhật quận huyện',
        'districts_delete' => 'Xóa quận huyện',
        'post_offices_create' => 'Tạo mới bưu cục',
        'post_offices_update' => 'Cập nhật bưu cục',
        'post_offices_delete' => 'Xóa bưu cục',
        'print_template_update' => 'Cập nhật mẫu in',
        'provinces_create' => 'Tạo mới tỉnh thành',
        'provinces_update' => 'Cập nhật tỉnh thành',
        'provinces_delete' => 'Xóa tỉnh thành',
        'wards_create' => 'Tạo mới phường xã',
        'wards_update' => 'Cập nhật phường xã',
        'wards_delete' => 'Xóa phường xã',
        'contacts_type_create' => 'Tạo mới loại yêu cầu hỗ trợ',
        'contacts_type_update' => 'Cập nhật loại yêu cầu hỗ trợ',
        'contacts_type_delete' => 'Xóa loại yêu cầu hỗ trợ',
        'order_settings_cod_delete' => 'Xóa cấu hình tiền cod',
        'order_fee_create' => 'Tạo mới tiền phát sinh',
        'order_fee_update' => 'Cập nhật tiền phát sinh',
        'order_fee_delete' => 'Xóa tiền phát sinh',
        'order_settings_insurance_delete' => 'Xóa cấu hình tiền bảo hiểm',
        'order_settings_pick_delete' => 'Xóa cấu hình tiền lấy hàng',
        'orders_create' => 'Tạo mới đơn hàng',
        'orders_update' => 'Cập nhật đơn hàng',
        'orders_import' => 'Import đơn hàng',
        'orders_export' => 'Export đơn hàng',
        'orders_delete' => 'Xóa đơn hàng',
        'order_services_create' => 'Tạo mới gói cước',
        'order_services_update' => 'Cập nhật gói cước',
        'order_services_delete' => 'Xóa gói cước',
        'shops_create' => 'Tạo mới shop',
        'shops_update' => 'Cập nhật shop',
        'shop_login' => 'Đăng nhập shop',
        'shop_logout' => 'Đăng xuất shop',
        'shops_login_api' => 'Đăng nhập shop qua api',
        'shops_logout_api' => 'Đăng xuất shop qua api',
        'shops_delete' => 'Xóa shop',
        'shop_staff_create' => 'Tạo mới nhân viên shop', 
        'shop_staff_update' => 'Cập nhật nhân viên shop',
        'shop_staff_delete' => 'Xóa nhân viên shop',
        'settings_cod_create' => 'Tạo mới cấu hình tiền cod',
        'settings_cod_update' => 'Cập nhật cấu hình tiền cod',
        'settings_pick_create' => 'Thêm mới cấu hình tiền lấy hàng',
        'settings_pick_update' => 'Cập nhật cấu hình tiền lấy hàng',
        'settings_insurance_create' => 'Thêm mới cấu hình tiền bảo hiểm',
        'settings_insurance_update' => 'Cập nhật cấu hình tiền bảo hiểm',
        'cash_flow_check' => 'Đối soát các đơn hàng',
        'setting_fee_update' => 'Cập nhật biểu phí',
        'change_password' => 'Thay đổi mật khẩu',
        'users_create' => 'Tạo mới quản trị viên',
        'users_update' => 'Cập nhật quản trị viên',
        'users_delete' => 'Xóa quản trị viên',
        'users_login' => 'Đăng nhập quản trị viên',
        'users_logout' => 'Đăng xuất quản trị viên',
    ];
}
