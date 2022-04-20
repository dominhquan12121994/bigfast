<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Services;

use App\Modules\Orders\Constants\OrderConstant;
use Throwable;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Redis;

use App\Helpers\OrderHelper;

use App\Modules\Orders\Models\Repositories\Contracts\OrderSettingInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface;

use App\Modules\Orders\Constants\OrderSettingConstant;
use App\Modules\Systems\Events\CreateLogEvents;

class SettingServices
{
    protected $_orderSettingInterface;
    protected $_orderServiceInterface;

    public function __construct(OrderSettingInterface $orderSettingInterface,
                                OrderServiceInterface $orderServiceInterface)
    {
        $this->_orderSettingInterface = $orderSettingInterface;
        $this->_orderServiceInterface = $orderServiceInterface;
    }

    public function updateSetting($data) 
    {
        try {
            DB::beginTransaction();

            $log_data = [];
            $fillData = [];
            $orderServices = $this->_orderServiceInterface->getMore(array('status' => 1));
            $regions = OrderSettingConstant::region;
            $fee_type = OrderSettingConstant::fee_type;
            foreach ( OrderSettingConstant::route as $keyRoute => $valRoute) {
                foreach ( $orderServices as $valService ) {
                    $result = [];
                    $fillSave = [];
                    $conditions = array(
                        'route'         => $keyRoute, 
                        'service'       => $valService->alias
                    );
                    $result['weight'] = [
                        'from' => '',
                        'to' => '',
                    ];
                    $result['time'] = [
                        'from' => '',
                        'to' => '',
                    ];
                    foreach ($regions as $key => $val) {
                        foreach ($fee_type as $f => $fv) {
                            $result['region'][$key][$f] = '';
                        }
                    }
                    $result['extra'] = '';
                    if (isset($data['weights'][$keyRoute][$valService->id])) {
                        $result['weight'] = $data['weights'][$keyRoute][$valService->id];
                        $result['region'] = $data['region'][$keyRoute][$valService->id];
                        $result['extra'] = $data['extra'][$keyRoute][$valService->id];
                        $result['time'] = $data['time'][$keyRoute][$valService->id];
                        $fillSave['result'] = $result;
                    }   
                    $fillSave['disable'] = isset($data['shows'][$keyRoute][$valService->id]) ? $data['shows'][$keyRoute][$valService->id] : 'off';

                    $item = $this->_orderSettingInterface->getOne($conditions);
                    if ($item) {
                        //Thêm dữ liệu log
                        $log_data[] = [
                            'old_data' => $item,
                        ];
                        
                        $item->fill($fillSave);
                        $item->save();
                    } else {
                        $fillData[] = [
                            'route'         => $keyRoute,
                            'service'       => $valService->alias,
                            'result'        => json_encode($result),
                            'disable'       => $fillSave['disable'],
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                        ];
                    }
                }
            }
            if ( count($fillData) > 0 ) {
                $this->_orderSettingInterface->insert($fillData);

                //Thêm dữ liệu log
                $log_data[] = [
                    'model' => $this->_orderSettingInterface,
                ];
            }

            DB::commit();

            //Lưu log 
            event(new CreateLogEvents( $log_data, 'setting_fee', 'setting_fee_update' ));

            $dataRes = new \stdClass();
            $dataRes->result = true;
        } catch (\Throwable $e) {
            DB::rollBack();
            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);

            $dataRes = new \stdClass();
            $dataRes->result = false;
            $dataRes->error = $messageBag;
        }
        return $dataRes;
    }
}
