<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Created by PhpStorm.
 * User: Electric
 * Date: 3/5/2021
 * Time: 2:32 PM
 */

namespace App\Modules\Orders\Imports;

use Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;

use App\Rules\PhoneRule;
use App\Helpers\AddressHelper;
use App\Modules\Orders\Jobs\CreateOrdersExcel;
use App\Modules\Orders\Models\Entities\OrderQueue;
use App\Modules\Orders\Exports\OrderImportSheet;

class OrdersFirstImportV2 implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $shop_id;
    protected $user_id;
    protected $user_type;
    protected $file_fails;
    protected $orderServices;

    function __construct($shop_id, $user_type, $user_id, $file_fails) {
        $this->shop_id = (int)$shop_id;
        $this->user_id = (int)$user_id;
        $this->user_type = $user_type;
        $this->file_fails = $file_fails;
    }

    public function collection(Collection $rows)
    {
        if (count($rows) < 101) {
            $arrData = array();
            if (count($rows->toArray()) > 0) {
                foreach ($rows->toArray() as $row) {
                    if (!empty(array_filter($row))) {
                        $arrData[] = $row;
                    }
                }
            }
            $validator = Validator::make($arrData, [
                '*.ten_nguoi_nhan' => 'required',
                '*.so_dien_thoai' => array('required', new PhoneRule()),
                '*.so_nhangongachhem_duongpho' => 'required',
                '*.phuongxa_quanhuyen_tinhthanh' => 'required',
                '*.goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru' => 'required|in:1,2,3',
                '*.tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0' => 'nullable|numeric|min:0',
                '*.yeu_cau_don_hang_1_cho_thu_hang_2_cho_xem_khong_thu_3_khong_cho_xem' => 'required|in:1,2,3',
                '*.khoi_luong_gram_toi_da_100000_gram' => 'required|numeric|min:1',
                '*.dai_cm' => 'nullable|numeric|min:0',
                '*.rong_cm' => 'nullable|numeric|min:0',
                '*.cao_cm' => 'nullable|numeric|min:0',
                '*.gia_tri_hang_hoa' => 'nullable|numeric|min:0',
                '*.shop_tra_ship' => 'nullable',
                '*.ma_don_hang_rieng' => 'nullable',
                '*.ten_hang_hoa' => 'required',
                '*.so_luong' => 'required|numeric|min:1',
                '*.ghi_chu_them' => 'nullable',
                '*.id_store' => 'nullable|numeric|min:0',
                '*.id_shop' => 'nullable|numeric|min:0',
            ], [
                '*.khoi_luong_gram_toi_da_100000_gram.required' => 'Chưa nhập khối lượng',
                '*.khoi_luong_gram_toi_da_100000_gram.numeric' => 'Khối lượng phải là kiểu số',
                '*.khoi_luong_gram_toi_da_100000_gram.min' => 'Khối lượng phải lớn hơn 0',
                '*.goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru.required' => 'Chưa chọn gói cước vận chuyển',
                '*.goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru.in' => 'Gói cước vận chuyển không tồn tại',
                '*.yeu_cau_don_hang_1_cho_xem_2_cho_thu_khong_thu_3_khong_cho_xem.required' => 'Chưa chọn yêu cầu đơn hàng',
                '*.yeu_cau_don_hang_1_cho_xem_2_cho_thu_khong_thu_3_khong_cho_xem.in' => 'Yêu cầu đơn hàng không tồn tại',
                '*.ten_hang_hoa.required' => 'Chưa nhập tên hàng hoá',
                '*.so_luong.required' => 'Chưa nhập số lượng hàng hoá',
                '*.so_luong.numeric' => 'Số lượng hàng hoá phải là kiểu số',
                '*.so_luong.min' => 'Khối lượng phải lớn hơn 1',
                '*.ten_nguoi_nhan.required' => 'Chưa nhập tên người nhận',
                '*.so_dien_thoai.required' => 'Chưa nhập số điện thoại',
                '*.so_nhangongachhem_duongpho.required' => 'Chưa nhập địa chỉ chi tiết',
                '*.phuongxa_quanhuyen_tinhthanh.required' => 'Chưa chọn địa chỉ',
                '*.tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0.required' => 'Tiền thu hộ phải là kiểu số',
                '*.tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0.numeric' => 'Tiền thu hộ phải là kiểu số',
                '*.tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0.min' => 'Tiền thu hộ phải lớn hơn 0',
                '*.dai_cm.numeric' => 'Chiều dài phải là kiểu số',
                '*.dai_cm.min' => 'Chiều dài phải lớn hơn 0',
                '*.rong_cm.numeric' => 'Chiều rộng phải là kiểu số',
                '*.rong_cm.min' => 'Chiều rộng phải lớn hơn 0',
                '*.cao_cm.numeric' => 'Chiều cao phải là kiểu số',
                '*.cao_cm.min' => 'Chiều cao phải lớn hơn 0',
                '*.gia_tri_hang_hoa.numeric' => 'Giá trị hàng hoá phải là kiểu số',
                '*.gia_tri_hang_hoa.min' => 'Giá trị hàng hoá phải lớn hơn 0',
            ]);

            $rowFails = array();
            $arrColFails = array();
            if ($validator->fails()) {
                $arrMess = array();
                $arrColFails = array_keys($validator->errors()->messages());
                foreach ($validator->errors()->messages() as $key => $value) {
                    $arrMess[$value[0]][] = (int)substr($key, 0, strpos($key, '.')) + 4;
                }

                $messToast = '';
                foreach ($arrMess as $mess => $rowErrors) {
                    $rowFails = array_merge($rowFails, array_values($rowErrors));
                    $messToast .= 'Dòng ' . implode(',', array_values($rowErrors)) . ' ' . $mess . '.</br>';
                }

                \Func::setToast('Thất bại', $messToast, 'error');
            }
            $rowFails = array_unique($rowFails);
            if (1===1){
                // check dia chi nguoi nhan
                $arrZoneValid = array();
                $arrRowsKey = array();
                $arrZoneIds = array();

                foreach ($rows as $key => $row) {
                    $newKey = $key + 4;
                    $arrRowsKey[] = $newKey;
                    if (in_array($newKey, $rowFails)) continue;
                    if ($this->user_type === 'user') {
                        if (!$this->shop_id) {
                            if (!isset($row['id_shop'])) continue;
                            if (!is_numeric($row['id_shop'])) continue;
                        }
                    }

                    $receiverZone = $row['phuongxa_quanhuyen_tinhthanh'];
                    $zoneIds = AddressHelper::mappingAddress($receiverZone);
                    if ($zoneIds) {
                        if (count($zoneIds) === 3) {
                            $arrZoneValid[] = $newKey;
                            $arrZoneValidCol[] = $key;
                            $arrZoneIds[$key] = $zoneIds;
                        }
                    }
                }

                $arrFail = array_diff($arrRowsKey, $arrZoneValid);
                if (count($arrFail) > 0) {
                    foreach ($arrFail as $rowFail) {
                        if (!in_array($rowFail, $rowFails)) {
                            $rowFails[] = $rowFail;
                            $arrColFails[] = ($rowFail - 4) . '.' . 'phuongxa_quanhuyen_tinhthanh';
                        }
                    }
                }

                if (count($rows) > count($arrFail)) {
                    foreach ($rows as $key => $row)
                    {
                        $newKey = $key + 4;
                        if (in_array($newKey, $rowFails)) continue;

                        $shop_id = $this->shop_id;
                        if ($this->user_type === 'user') {
                            if (!$this->shop_id && isset($row['id_shop'])) {
                                if (is_numeric($row['id_shop'])) {
                                    $shop_id = $row['id_shop'];
                                }
                            }
                        }

                        $row['user_id'] = $this->user_id;
                        $row['shop_id'] = (int)$shop_id;
                        $row['user_type'] = $this->user_type;
                        $row['created_date'] = (int)date('Ymd');
                        $row['receiver_province'] = $arrZoneIds[$key][0];
                        $row['receiver_district'] = $arrZoneIds[$key][1];
                        $row['receiver_ward'] = $arrZoneIds[$key][2];
                        $orderQueue = OrderQueue::create([
                            'status'            => 0,
                            'shop_id'           => $row['shop_id'],
                            'created_date'      => $row['created_date'],
                            'receiver_name'     => $row['ten_nguoi_nhan'],
                            'receiver_phone'    => $row['so_dien_thoai'],
                            'receiver_address'  => $row['so_nhangongachhem_duongpho'],
                            'cod'               => $row['tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0'],
                            'client_code'       => $row['ma_don_hang_rieng'],
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ]);
                        $row['queue_id'] = $orderQueue->id;
                        CreateOrdersExcel::dispatch($row)->onQueue('createOrdersExcel');
                    }

                    if (count($arrFail) > 0) {
                        $filename = $this->file_fails ?: 'orders-fails.xlsx';
                        Excel::store(new OrderImportSheet($rows, $arrColFails), $filename);
                        request()->session()->put('orders-fails-excel', $filename);
                        \Func::setToast('Thành công', 'Upload thành công<br/>Tìm thấy ' . (count($rows) - count($arrFail)) . ' yêu cầu lên đơn hàng hợp lệ.<br/>Có ' . count($arrFail) . ' dòng lỗi.<br/>Hãy kiểm tra trạng thái yêu cầu và file lỗi được tải xuống!', 'notice');
                    } else {
                        \Func::setToast('Thành công', 'Upload thành công<br/>Tìm thấy ' . (count($rows) - count($arrFail)) . ' yêu cầu lên đơn hàng hợp lệ.<br/>Hãy kiểm tra trạng thái yêu cầu!', 'notice');
                    }
                } elseif (count($rows) === 0) {
                    \Func::setToast('Thất bại', 'File chưa có đơn hàng', 'error');
                } else {
                    \Func::setToast('Thất bại', 'File lỗi hoàn toàn!', 'error');
                }
            }
        } else {
            \Func::setToast('Thất bại', 'Tối đa 100 đơn/file', 'error');
        }
    }


    public function rules(): array
    {
        return [
            // Above is alias for as it always validates in batches
            '*.ten_nguoi_nhan' => 'required',
            '*.so_dien_thoai' => 'required',
            '*.so_nhangongachhem_duongpho' => 'required',
            '*.phuongxa_quanhuyen_tinhthanh' => 'required',
            '*.goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru' => 'required',
            '*.tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0' => 'nullable|numeric',
            '*.yeu_cau_don_hang_1_cho_xem_2_cho_thu_khong_thu_3_khong_cho_xem' => 'required',
            '*.khoi_luong_gram_toi_da_100000_gram' => 'required|numeric',
            '*.dai_cm' => 'nullable|numeric',
            '*.rong_cm' => 'nullable|numeric',
            '*.cao_cm' => 'nullable|numeric',
            '*.gia_tri_hang_hoa' => 'nullable|numeric',
            '*.shop_tra_ship' => 'nullable',
            '*.ma_don_hang_rieng' => 'nullable',
            '*.ten_hang_hoa' => 'required',
            '*.so_luong' => 'required|numeric',
            '*.ghi_chu_them' => 'nullable',
        ];
    }

    public function headingRow(): int
    {
        return 3;
    }
}
