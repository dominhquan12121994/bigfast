<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Services;

use Exception;
use Throwable;
use Illuminate\Support\MessageBag;
use App\Modules\Orders\Constants\OrderSettingConstant;
use App\Modules\Operators\Models\Repositories\Contracts\ProvincesInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingInterface;
use App\Modules\Operators\Models\Repositories\Contracts\DistrictsInterface;

class CalculatorFeeServices
{
    public function __construct(ProvincesInterface $provinceInterface,
                                OrderSettingInterface $orderSettingInterface,
                                DistrictsInterface $districtInterface)
    {
        $this->_orderSettingInterface = $orderSettingInterface;
        $this->_provinceInterface = $provinceInterface;
        $this->_districtInterface = $districtInterface;
    }

    public function calculatorFee($payload = array())
    {
        try {
            $extra = OrderSettingConstant::extra;
            $regions = OrderSettingConstant::region;
            $lienvungdacbiet = OrderSettingConstant::lienvungdacbiet;

            $send_data = $this->_provinceInterface->getById((int)$payload['p_id_send']);
            if (!$send_data) {
                throw new Exception('Không tìm thấy tỉnh thành người gửi');
            }
            $zone_send = $send_data->zone;
            $p_code_send = $send_data->code;

            $receive_data = $this->_provinceInterface->getById((int)$payload['p_id_receive']);
            if (!$receive_data) {
                throw new Exception('Không tìm thấy tỉnh thành người nhận');
            }
            $zone_receive = $receive_data->zone;
            $p_code_receive = $receive_data->code;

            if ($p_code_send === $p_code_receive) {
                $route = 'noitinh';
            } elseif (isset($lienvungdacbiet[$p_code_send]) && isset($lienvungdacbiet[$p_code_receive])) {
                $route = 'lienvungdacbiet';
            } elseif (isset($lienvungdacbiet[$p_code_send]) || isset($lienvungdacbiet[$p_code_receive])) {
                if ($zone_send === $zone_receive) {
                    $route = 'noivung';
                } else {
                    $route = 'lienvung';
                }
            } else {
                if ($zone_send === $zone_receive) {
                    $route = 'noivungtinh';
                } else {
                    $route = 'lienvungtinh';
                }
            }

            $dataSetting = $this->_orderSettingInterface->getOne(
                array(
                'service' => $payload['service'],
                'route' => $route,
                'disable' => 'off'
                )
            );

            if (!$dataSetting) {
                throw new Exception('Chưa có biểu phí tương ứng');
            }

            $grow = 0;
            $data = json_decode($dataSetting->result);
            if ((int)$payload['weight'] > $data->weight->to) {
                $grow = ceil(((int)$payload['weight'] - $data->weight->to) / ($extra * 1000));
            }

            $result = $data->extra * $grow;
            $districtReceiver = $this->_districtInterface->getById((int)$payload['d_id_receive']);
            if (!$districtReceiver) {
                throw new Exception('Không tìm thấy quận huyện người nhận');
            }
            $districtReceiverType = $districtReceiver->type;
            if (!isset($regions[$districtReceiverType])) {
                throw new Exception('Dữ liệu không hợp lệ');
            }

            $fee_transport = $data->region->$districtReceiverType->fee_transport;

            $result += $fee_transport;

            $dataRes = new \stdClass();
            $dataRes->status = true;
            $dataRes->result = $result;
            $dataRes->timePick = $data->time;

        } catch (\Throwable $e) {
            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->status = false;
            $dataRes->error = $messageBag;
        }

        return $dataRes;
    }
}
