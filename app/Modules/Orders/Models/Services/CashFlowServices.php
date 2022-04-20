<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Models\Services;

use Exception;
use Throwable;
use Illuminate\Support\MessageBag;

use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShopReconcileInterface;

class CashFlowServices
{
    protected $_shopsInterface;
    protected $_ordersInterface;
    protected $_shopBankInterface;
    protected $_shopReconcileInterface;

    public function __construct(ShopBankInterface $shopBankInterface,
                                ShopsInterface $shopsInterface,
                                OrdersInterface $ordersInterface,
                                OrderShopReconcileInterface $shopReconcileInterface)
    {
        $this->_shopsInterface = $shopsInterface;
        $this->_ordersInterface = $ordersInterface;
        $this->_shopBankInterface = $shopBankInterface;
        $this->_shopReconcileInterface = $shopReconcileInterface;
    }

    public function calculator($payload = array())
    {
        try {
            // lay danh sach shop
            $arrDataCash = array();
            $dateCheck = $payload['calculatorDate'];
            // get condition by cycle_cod
            $getThu = strtolower(date('l', strtotime($dateCheck)));

            $cycle_cod = array();
            if ($getThu === 'friday') {
                $cycle_cod[] = 'friday';
            }
            if ($getThu === 'tuesday') {
                $cycle_cod[] = 'tuesday_friday';
            }
            if ($getThu === 'monday' || $getThu === 'wednesday' || $getThu === 'friday') {
                $cycle_cod[] = 'monday_wednesday_friday';
            }
            $lastOfMonth = date('t');
            $currentDay = date('d');
            if ($lastOfMonth == $currentDay) {
                $cycle_cod[] = 'once_per_month';
                $cycle_cod[] = 'twice_per_month';
            }
            if (15 == $currentDay) {
                $cycle_cod[] = 'twice_per_month';
            }

            $conditions = array('date_reconcile' => $dateCheck, 'cycle_cod' => $cycle_cod);
            if (isset($payload['ids'])) {
                if (count($payload['ids']) > 0) {
                    $conditions['id'] = $payload['ids'];
                }
            }
            if (isset($payload['idOnce'])) {
                $conditions = array('id' => $payload['idOnce']);
            }
            $shops = $this->_shopBankInterface->getMore($conditions);
            // dd($shops);
            if (count($shops) > 0) {
                foreach ($shops as $shop) {
                    $beginDate = date('Ymd', strtotime($shop->date_reconcile));

                    /*
                     * Lay don hang duoc tao trong khoang thoi gian
                     */
                    $filter = array('shop_id' => $shop->id);
                    $filter['date_range'] = array((int)$beginDate, (int)date('Ymd', strtotime($dateCheck)));

                    $arrReconcile = $this->_shopReconcileInterface->getMore($filter);
                    if (count($arrReconcile) > 0) {
                        $fee_transport = $arrReconcile->sum('fee_transport');
                        $fee_insurance = $arrReconcile->sum('fee_insurance');
                        $fee_cod = $arrReconcile->sum('fee_cod');
                        $fee_refund = $arrReconcile->sum('fee_refund');
                        $fee_store = $arrReconcile->sum('fee_store');
                        $fee_change_info = $arrReconcile->sum('fee_change_info');
                        $fee_transfer = $arrReconcile->sum('fee_transfer');
                        $refund_cod = $arrReconcile->sum('refund_cod');
                        $refund_transport = $arrReconcile->sum('refund_transport');

                        $total_fee = $fee_transport + $fee_insurance + $fee_cod + $fee_refund + $fee_store + $fee_change_info + $fee_transfer;
                        $money_indemnify = $arrReconcile->sum('money_indemnify') + $refund_cod + $refund_transport;
                        $total_cod = $arrReconcile->sum('total_cod');

                        $item = new \stdClass();
                        $item->total_fee = $total_fee;
                        $item->total_cod = $total_cod;
                        $item->money_indemnify = $money_indemnify;
                        $item->total_du = $total_cod + $money_indemnify - $total_fee;
                        $item->shop = $this->_shopsInterface->getById($shop->id);
                        $item->timeRange = $filter['date_range'];
                        $item->shop_bank = $shop;
                        $item->fullInfo = true;
                        if ( $shop->bank_name == '' || $shop->bank_branch == '' || $shop->stk_name == '' || $shop->stk == '' ) {
                            $item->fullInfo = false;
                        }

                        $arrDataCash[] = $item;
                    }
                }
            }

            $dataRes = new \stdClass();
            $dataRes->status = true;
            $dataRes->result = $arrDataCash;

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
