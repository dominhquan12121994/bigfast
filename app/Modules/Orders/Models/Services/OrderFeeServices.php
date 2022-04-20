<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Services;

use DB;
use Exception;
use Throwable;
use Illuminate\Support\MessageBag;

use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShopReconcileInterface;

use App\Modules\Orders\Models\Repositories\Contracts\ShopReconcileInterface;
use App\Modules\Systems\Events\CreateLogEvents;

class OrderFeeServices
{

    protected $_shopsInterface;
    protected $_shopReconcileInterface;
    protected $_orderShopReconcileInterface;

    public function __construct(ShopReconcileInterface $shopReconcileInterface,
                                ShopBankInterface $shopBankInterface,
                                OrderFeeInterface $orderFeeInterface,
                                ShopsInterface $shopsInterface)
    {
        $this->_shopReconcileInterface = $shopReconcileInterface;
        $this->_shopBankInterface = $shopBankInterface;
        $this->_shopsInterface = $shopsInterface;
        $this->_orderFeeInterface = $orderFeeInterface;
    }

    public function crudUpdate($request, $id)
    {
        try {
            $old_data = $this->_orderFeeInterface->getById($id);
            $orderFee = $this->_orderFeeInterface->updateById($id, array(
                'fee_type' => $request->input('fee_type'),
                'date' => (int)date('Ymd', strtotime($request->input('date'))),
                'value' => (int)$request->input('value'),
            ));

            //Thêm dữ liệu log
            $log_data[] = [
                'old_data' => $old_data,
            ];
            //Lưu log 
            event(new CreateLogEvents( $log_data, 'order_fee', 'order_fee_update' ));

            return true;
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageBag = new MessageBag;
            $messageBag->add('error', $message);
            return redirect()->back()->withInput()->withErrors($messageBag);
        }
    }

    public function getOrderFee($payload = array()) {
        if ($payload['shop_id'] == 0) {
            $conditions = array();
        } else {
            $conditions = array(
                'shop_id' => (int)$payload['shop_id']
            );
        }

        $fetchOptions = array(
            'orderBy' => 'end_date',
            'direction' => 'DESC',
        );

        $result = $this->_shopReconcileInterface->getMore($conditions, $fetchOptions, 10);

        foreach($result as $key => $value) {
            $result[$key]->shopInfo = $this->_shopsInterface->getById((int)$value['shop_id']);
        }
        return $result;
    }

    public function getFeeIncurred($ordersList, $aryFeeType, $customPaginate = '') {
        $result = array();
        if ($ordersList instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $aryOrder = $ordersList->pluck('id')->all();
        } else {
            if ($customPaginate === 'customPaginate') {
                $aryOrder = $ordersList['data']->pluck('id')->all();
            } else {
                $aryOrder = array($ordersList->id);
            }
        }

        $conditions['order_id'] = $aryOrder;
        $conditions['fee_type'] = $aryFeeType;
        $feeIncurred = $this->_orderFeeInterface->getMore($conditions);

        foreach ( $feeIncurred as $fee ) {
            if ( isset ($result[$fee->order_id]) ) {
                if ( isset ( $result[$fee->order_id][$fee->fee_type] )) {
                    $result[$fee->order_id][$fee->fee_type] += $fee->value;
                } else {
                    $result[$fee->order_id][$fee->fee_type] = $fee->value;
                }
            } else {
                $result[$fee->order_id][$fee->fee_type] = $fee->value;
            }
        }
        
        return $result;
    }
}
