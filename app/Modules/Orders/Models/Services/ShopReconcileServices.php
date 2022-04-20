<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Services;

use DB;
use Exception;
use Throwable;
use Illuminate\Support\MessageBag;
use App\Modules\Orders\Jobs\ChangeStatus;

use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopReconcileInterface;

class ShopReconcileServices
{

    protected $_shopsInterface;
    protected $_ordersInterface;
    protected $_shopReconcileInterface;
    protected $_orderShopReconcileInterface;

    public function __construct(ShopReconcileInterface $shopReconcileInterface,
                                ShopBankInterface $shopBankInterface,
                                OrdersInterface $ordersInterface,
                                ShopsInterface $shopsInterface)
    {
        $this->_shopReconcileInterface = $shopReconcileInterface;
        $this->_shopBankInterface = $shopBankInterface;
        $this->_shopsInterface = $shopsInterface;
        $this->_ordersInterface = $ordersInterface;
    }

    public function storeReconcile($payload = array(), $user_id = 0)
    {
        try {
            DB::beginTransaction();

            foreach ($payload as $key => $value) {
                $this->_shopReconcileInterface->create(array(
                    'begin_date' => (int)date('Ymd', strtotime($value['begin_date'])),
                    'end_date' => (int)date('Ymd', strtotime($value['end_date'])),
                    'shop_id' => (int)$value['shop_id'],
                    'total_fee' => (int)$value['total_fee'],
                    'total_cod' => (int)$value['total_cod'],
                    'money_indemnify' => (int)$value['money_indemnify'],
                    'total_du' => (int)$value['total_du'],
                    'user_reconcile' => (int)$value['user_reconcile'],
                ));
                $data['date_reconcile'] = date('Y-m-d', strtotime($value['end_date'] . ' +1 day'));
                $this->_shopBankInterface->updateById((int)$value['shop_id'], $data);

                // update in table orders
//                $this->_ordersInterface->updateByCondition(
//                    array(
//                        'shop_id' => (int)$value['shop_id'],
//                        'status' => 9,
//                        'status_detail' => 91,
//                        'collect_money_range' => array(
//                            (int)date('Ymd', strtotime($value['begin_date'])),
//                            (int)date('Ymd', strtotime($value['end_date'])),
//                        ),
//                    ),
//                    array(
//                        'status' => 8,
//                        'status_detail' => 81
//                    ),
//                    array(),
//                    true
//                );
                $payloadJob = array(
                    'shop_id' => (int)$value['shop_id'],
                    'from_status' => 9,
                    'from_status_detail' => 91,
                    'collect_money_range' => array(
                        (int)date('Ymd', strtotime($value['begin_date'])),
                        (int)date('Ymd', strtotime($value['end_date'])),
                    ),
                    'to_status' => 8,
                    'to_status_detail' => 81,
                    'log_type'  => 'reconcile_send',
                    'log_note'  => 'Đối soát giao hàng',
                    'user_type' => 'user',
                    'user_id' => $user_id
                );
                ChangeStatus::dispatch($payloadJob)->onQueue('changeStatus');

//                $this->_ordersInterface->updateByCondition(
//                    array(
//                        'shop_id' => (int)$value['shop_id'],
//                        'status' => 7,
//                        'status_detail' => 73,
//                        'collect_money_range' => array(
//                            (int)date('Ymd', \strtotime($value['begin_date'])),
//                            (int)date('Ymd', \strtotime($value['end_date'])),
//                        ),
//                    ),
//                    array(
//                        'status' => 8,
//                        'status_detail' => 83
//                    ),
//                    array(),
//                    true
//                );
                $payloadJob = array(
                    'shop_id' => (int)$value['shop_id'],
                    'from_status' => 7,
                    'from_status_detail' => 73,
                    'collect_money_range' => array(
                        (int)date('Ymd', strtotime($value['begin_date'])),
                        (int)date('Ymd', strtotime($value['end_date'])),
                    ),
                    'log_type'  => 'reconcile_missing',
                    'log_note'  => 'Đối soát thất lạc',
                    'to_status' => 8,
                    'to_status_detail' => 83,
                    'user_type' => 'user',
                    'user_id' => $user_id
                );
                ChangeStatus::dispatch($payloadJob)->onQueue('changeStatus');

//                $this->_ordersInterface->updateByCondition(
//                    array(
//                        'shop_id' => (int)$value['shop_id'],
//                        'status' => 7,
//                        'status_detail' => 74,
//                        'collect_money_range' => array(
//                            (int)date('Ymd', \strtotime($value['begin_date'])),
//                            (int)date('Ymd', \strtotime($value['end_date'])),
//                        ),
//                    ),
//                    array(
//                        'status' => 8,
//                        'status_detail' => 84
//                    ),
//                    array(),
//                    true
//                );
                $payloadJob = array(
                    'shop_id' => (int)$value['shop_id'],
                    'from_status' => 7,
                    'from_status_detail' => 74,
                    'collect_money_range' => array(
                        (int)date('Ymd', strtotime($value['begin_date'])),
                        (int)date('Ymd', strtotime($value['end_date'])),
                    ),
                    'log_type'  => 'reconcile_damaged',
                    'log_note'  => 'Đối soát hư hỏng',
                    'to_status' => 8,
                    'to_status_detail' => 84,
                    'user_type' => 'user',
                    'user_id' => $user_id
                );
                ChangeStatus::dispatch($payloadJob)->onQueue('changeStatus');
                // ..update in table orders

            }
            DB::commit();
            $result = 1;
        } catch (Throwable $e) {
            DB::rollBack();
            $result = 0;
        }
        return $result;
    }

    public function getReconcile($payload = array()) {
        if ($payload['shop_id'] === 0) {
            $conditions = array(
                'date_range' => $payload['date_range']
            );
        } else {
            $conditions = array(
                'shop_id' => (int)$payload['shop_id'],
                'date_range' => $payload['date_range']
            );
        }

        $fetchOptions = array(
            'with' => array(
                'shop'
            ),
            'orderBy' => 'end_date',
            'direction' => 'DESC',
        );

        $shopReconciles = $this->_shopReconcileInterface->getMore($conditions, $fetchOptions);

        return $shopReconciles;
    }
}
