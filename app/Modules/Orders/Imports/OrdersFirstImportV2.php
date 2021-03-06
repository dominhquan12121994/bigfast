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
                '*.khoi_luong_gram_toi_da_100000_gram.required' => 'Ch??a nh???p kh???i l?????ng',
                '*.khoi_luong_gram_toi_da_100000_gram.numeric' => 'Kh???i l?????ng ph???i l?? ki???u s???',
                '*.khoi_luong_gram_toi_da_100000_gram.min' => 'Kh???i l?????ng ph???i l???n h??n 0',
                '*.goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru.required' => 'Ch??a ch???n g??i c?????c v???n chuy???n',
                '*.goi_cuoc_1_giao_hang_nhanh_2_giao_hang_sieu_toc_3_giao_hang_vu_tru.in' => 'G??i c?????c v???n chuy???n kh??ng t???n t???i',
                '*.yeu_cau_don_hang_1_cho_xem_2_cho_thu_khong_thu_3_khong_cho_xem.required' => 'Ch??a ch???n y??u c???u ????n h??ng',
                '*.yeu_cau_don_hang_1_cho_xem_2_cho_thu_khong_thu_3_khong_cho_xem.in' => 'Y??u c???u ????n h??ng kh??ng t???n t???i',
                '*.ten_hang_hoa.required' => 'Ch??a nh???p t??n h??ng ho??',
                '*.so_luong.required' => 'Ch??a nh???p s??? l?????ng h??ng ho??',
                '*.so_luong.numeric' => 'S??? l?????ng h??ng ho?? ph???i l?? ki???u s???',
                '*.so_luong.min' => 'Kh???i l?????ng ph???i l???n h??n 1',
                '*.ten_nguoi_nhan.required' => 'Ch??a nh???p t??n ng?????i nh???n',
                '*.so_dien_thoai.required' => 'Ch??a nh???p s??? ??i???n tho???i',
                '*.so_nhangongachhem_duongpho.required' => 'Ch??a nh???p ?????a ch??? chi ti???t',
                '*.phuongxa_quanhuyen_tinhthanh.required' => 'Ch??a ch???n ?????a ch???',
                '*.tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0.required' => 'Ti???n thu h??? ph???i l?? ki???u s???',
                '*.tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0.numeric' => 'Ti???n thu h??? ph???i l?? ki???u s???',
                '*.tien_thu_ho_neu_khong_co_thu_ho_thi_nhap_0.min' => 'Ti???n thu h??? ph???i l???n h??n 0',
                '*.dai_cm.numeric' => 'Chi???u d??i ph???i l?? ki???u s???',
                '*.dai_cm.min' => 'Chi???u d??i ph???i l???n h??n 0',
                '*.rong_cm.numeric' => 'Chi???u r???ng ph???i l?? ki???u s???',
                '*.rong_cm.min' => 'Chi???u r???ng ph???i l???n h??n 0',
                '*.cao_cm.numeric' => 'Chi???u cao ph???i l?? ki???u s???',
                '*.cao_cm.min' => 'Chi???u cao ph???i l???n h??n 0',
                '*.gia_tri_hang_hoa.numeric' => 'Gi?? tr??? h??ng ho?? ph???i l?? ki???u s???',
                '*.gia_tri_hang_hoa.min' => 'Gi?? tr??? h??ng ho?? ph???i l???n h??n 0',
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
                    $messToast .= 'D??ng ' . implode(',', array_values($rowErrors)) . ' ' . $mess . '.</br>';
                }

                \Func::setToast('Th???t b???i', $messToast, 'error');
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
                        \Func::setToast('Th??nh c??ng', 'Upload th??nh c??ng<br/>T??m th???y ' . (count($rows) - count($arrFail)) . ' y??u c???u l??n ????n h??ng h???p l???.<br/>C?? ' . count($arrFail) . ' d??ng l???i.<br/>H??y ki???m tra tr???ng th??i y??u c???u v?? file l???i ???????c t???i xu???ng!', 'notice');
                    } else {
                        \Func::setToast('Th??nh c??ng', 'Upload th??nh c??ng<br/>T??m th???y ' . (count($rows) - count($arrFail)) . ' y??u c???u l??n ????n h??ng h???p l???.<br/>H??y ki???m tra tr???ng th??i y??u c???u!', 'notice');
                    }
                } elseif (count($rows) === 0) {
                    \Func::setToast('Th???t b???i', 'File ch??a c?? ????n h??ng', 'error');
                } else {
                    \Func::setToast('Th???t b???i', 'File l???i ho??n to??n!', 'error');
                }
            }
        } else {
            \Func::setToast('Th???t b???i', 'T???i ??a 100 ????n/file', 'error');
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
