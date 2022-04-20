<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Services;

use Picqer\Barcode\BarcodeGeneratorPNG;

use App\Modules\Orders\Models\Services\OrderServices;

use App\Modules\Operators\Constants\PrintTemplatesConstant;
use App\Modules\Orders\Constants\OrderConstant;

use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;

class PrintTemplatesServices
{
    protected $_orderServices;
    protected $_orderServiceInterface;

    public function __construct(OrderServices $orderServices, OrderServiceInterface $orderServiceInterface)
    {
        $this->_orderServices = $orderServices;
        $this->_orderServiceInterface = $orderServiceInterface;
    }

    public function listCode()
    {
        $lists = [];
        $codes = PrintTemplatesConstant::key_word;
        $barCode = new BarcodeGeneratorPNG();
        $barCode->useGd();

        foreach ($codes as $list) {
            foreach ($list['lists'] as $key => $val ) {
                if ( isset($val['barcode']) ) {
                    $val['default'] = "<img src='data:image/png;base64," . base64_encode($barCode->getBarcode($val['default'], config('barcode.'.$val['barcode']), 2, 50)) . "'>";
                }
                $val['pAlias'] = $list['alias'];
                $lists[$key] = $val;
            }
        }

        return $lists;
    }

    public function convertCode($setting, $order_id = '', $real = false)
    {
        $data = [];
        $barCode = new BarcodeGeneratorPNG();
        $barCode->useGd();

        $order = null;
        $lists = self::listCode();
        $lists_order = explode(',', $order_id);
        foreach ($lists_order as $index => $id) {
            $setting->htmlConvert = $setting->html;
            //Lấy DS code cần thay thế
            $aryCode = null;
            preg_match_all('/{__.*?__}/', $setting->htmlConvert, $aryCode);
            $aryCode = $aryCode[0];

            if ($id != 0) {
                $order = json_decode($this->_orderServices->getOne($id), true);
            }

            foreach ( $aryCode as $code) {
                $key = $code;
                if ( !isset($lists[$key]) ) {
                    continue;
                }
                $val = $lists[$key];

                $replace = $val['default'];
                if ($real) {
                    //Xử lý TH có alias
                    $getData = $order['data'][$val['pAlias']];

                    if (isset($val['alias'])) {
                        $replace = $getData[$val['alias']];
                        if ($key == '{__GOI_CUOC__}') {
                            $replace = $this->_orderServiceInterface->getOne(array('alias' => $replace))->name;
                        }
                    } else {
                        $products = $getData;

                        if ($key == '{__NGAY_HIEN_TAI__}') {
                            $replace = date('d-m-Y');
                        }
                        if ($key == '{__DANH_SACH_SP__}') {
                            $aryName = array_column($products, 'name');
                            $html = implode(',', $aryName);
                            $replace = $html;
                        }
                        if ($key == '{__KHOI_LUONG_SP__}') {
                            $getData = $order['data']['info'];
                            $replace = $getData['weight']. ' gram';
                        }
                        if ($key == '{__SL_SP__}') {
                            $replace = $getData;
                            $count = 0;
                            foreach ($products as $product) {
                                $count += $product['quantity'];
                            }
                            $replace = $count;
                        }
                        if ($key == '{__LUU_Y__}') {
                            $getData = $order['data']['extra'];
                            $replace = OrderConstant::notes[$getData['note1']];
                        }
                        if ($key == '{__GHI_CHU__}') {
                            $getData = $order['data']['extra'];
                            $replace = $getData['note2'];
                        }
                        if ($key == '{__CLIENT_CODE__}') {
                            $getData = $order['data']['extra'];
                            $replace = $getData['client_code'];
                        }
                        if ($key == '{__TONG_THU__}') {
                            $replace = $getData;
                            $replace = number_format($replace['cod'] + (($replace['payfee'] === 'payfee_receiver') ? $replace['transport_fee'] : 0)) . 'vnd';
                        }
                        if ($key == '{__NGAY_LAY_HANG_DU_KIEN__}') {
                            $getData = $order['data']['extra'];
                            $replace = date('d-m-Y H:i', strtotime($getData['expect_receiver']));
                        }
                        if ($key == '{__TONG_TIEN_SP__}') {
                            $replace = $getData;
                            $count = 0;
                            foreach ($products as $product) {
                                $count += $product['price'];
                            }
                            $replace = number_format($count). ' vnd';
                        }
                    }
                    //Xử lý XSS dữ liệu In
                    if ( !in_array($key, array('{__LOGO_DOANH_NGHIEP__}', '{__LOGO_DOANH_NGHIEP_SMALL__}')) ) {
                        $replace = strip_tags($replace);
                        $replace = htmlspecialchars($replace);
                    }
                    if ( isset($val['barcode']) ) {
                        $replace = "<img src='data:image/png;base64," . base64_encode($barCode->getBarcode($replace, config('barcode.'.$val['barcode']), 2, 50)) . "'>";
                    }

                }
                $setting->htmlConvert = str_replace($key, $replace, $setting->htmlConvert);
            }
            $data['data'][] = $setting->toArray();
        }

        return $data;
    }
}
