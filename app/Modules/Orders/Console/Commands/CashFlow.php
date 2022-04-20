<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Orders\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderFeeShopInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface;
use App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface;
use App\Modules\Orders\Models\Repositories\Contracts\OrderShopReconcileInterface;

class CashFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cash-flow {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cash Flow';

    protected $_shopsInterface;
    protected $_ordersInterface;
    protected $_orderFeeInterface;
    protected $_orderFeeShopInterface;
    protected $_shopBankInterface;
    protected $_shopReconcileInterface;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ShopBankInterface $shopBankInterface,
                                ShopsInterface $shopsInterface,
                                OrdersInterface $ordersInterface,
                                OrderFeeInterface $orderFeeInterface,
                                OrderFeeShopInterface $orderFeeShopInterface,
                                OrderShopReconcileInterface $shopReconcileInterface)
    {
        parent::__construct();

        $this->_shopsInterface = $shopsInterface;
        $this->_ordersInterface = $ordersInterface;
        $this->_orderFeeInterface = $orderFeeInterface;
        $this->_orderFeeShopInterface = $orderFeeShopInterface;
        $this->_shopBankInterface = $shopBankInterface;
        $this->_shopReconcileInterface = $shopReconcileInterface;
    }

    /**
     * Lay danh sach Shop den ky han doi soat => Lay khoang ngay doi soat cua Shop => Lay cac loai Phi theo Order, theo Shop
     *
     * @return mixed
     */
    public function handle()
    {
        $date = $this->argument('date');
        if (!$date) {
            $date = date('Ymd');
        }

        $this->info('=============BEGIN============');
        try {
            DB::beginTransaction();

//            // lay danh sach shop
            $arrDataCash = array();
//            $dateCheck = date('Y-m-d', strtotime('0 day'));
//            $conditions = array('date_reconcile' => $dateCheck);
//            $shopBank = $this->_shopBankInterface->getMore($conditions);

            $shops = DB::table('order_fee')
                ->select('shop_id')
                ->where('date', (int)$date)
                ->groupBy('shop_id')
                ->get();

            if (count($shops) > 0) {
                foreach ($shops as $shop) {
                    $date = (int)$date;
                    $shop_id = $shop->shop_id;
                    $filter = array('shop_id' => $shop_id, 'date' => $date);
                    $ordersFee = $this->_orderFeeInterface->getMore($filter, array('with' => 'order'));
                    $ordersFeeShop = $this->_orderFeeShopInterface->getMore($filter);

                    // Phi theo don hang
                    $total_cod = $ordersFee->filter(function ($item, $key) {
                        return $item->fee_type === 'total_cod' || $item->fee_type === 'incurred_total_cod';
                    })->sum('value');
                    $money_indemnify = $ordersFee->filter(function ($item, $key) {
                        return $item->fee_type === 'incurred_money_indemnify';
                    })->sum('value');
                    $fee_transport = $ordersFee->filter(function ($item, $key) {
                        if ( $item->order->payfee === 'payfee_receiver' ) {
                            return $item->fee_type === 'transport';
                        }
                        return $item->fee_type === 'transport' || $item->fee_type === 'incurred_fee_transport';
                    })->sum('value');
                    $fee_cod = $ordersFee->filter(function ($item, $key) {
                        return $item->fee_type === 'cod' || $item->fee_type === 'incurred_fee_cod';
                    })->sum('value');
                    $fee_insurance = $ordersFee->filter(function ($item, $key) {
                        return $item->fee_type === 'insurance' || $item->fee_type === 'incurred_fee_insurance';
                    })->sum('value');
                    $refund_cod = $ordersFee->filter(function ($item, $key) {
                        return $item->fee_type === 'refund_cod' || $item->fee_type === 'incurred_refund_cod';
                    })->sum('value');
                    $refund_transport = $ordersFee->filter(function ($item, $key) {
                        return $item->fee_type === 'refund_transport' || $item->fee_type === 'incurred_refund_transport';
                    })->sum('value');

                    // Phi theo shop
                    $fee_pick = $ordersFeeShop->filter(function ($item, $key) {
                        return $item->fee_type === 'pick' || $item->fee_type === 'incurred_fee_pick';
                    })->sum('value');
                    $fee_forward = $ordersFeeShop->filter(function ($item, $key) {
                        return $item->fee_type === 'forward' || $item->fee_type === 'incurred_fee_forward';
                    })->sum('value');
                    $fee_change_info = $ordersFeeShop->filter(function ($item, $key) {
                        return $item->fee_type === 'change_info' || $item->fee_type === 'incurred_fee_change_info';
                    })->sum('value');
                    $fee_store = $ordersFeeShop->filter(function ($item, $key) {
                        return $item->fee_type === 'store' || $item->fee_type === 'incurred_fee_store';
                    })->sum('value');
                    $fee_refund = $ordersFeeShop->filter(function ($item, $key) {
                        return $item->fee_type === 'refund' || $item->fee_type === 'incurred_fee_refund';
                    })->sum('value');
                    $fee_transfer = $ordersFeeShop->filter(function ($item, $key) {
                        return $item->fee_type === 'transfer' || $item->fee_type === 'incurred_fee_transfer';
                    })->sum('value');

                    $item = new \stdClass();
                    $item->shop_id = $shop_id;
                    $item->total_cod = $total_cod;
                    $item->money_indemnify = $money_indemnify;
                    $item->fee_transport = $fee_transport;
                    $item->fee_cod = $fee_cod;
                    $item->fee_insurance = $fee_insurance;
                    $item->fee_pick = $fee_pick;
                    $item->fee_forward = $fee_forward;
                    $item->fee_change_info = $fee_change_info;
                    $item->fee_store = $fee_store;
                    $item->fee_refund = $fee_refund;
                    $item->fee_transfer = $fee_transfer;
                    $item->refund_cod = $refund_cod;
                    $item->refund_transport = $refund_transport;
                    $arrDataCash[] = $item;

                    $checkExists = $this->_shopReconcileInterface->getOne(array('shop_id' => $shop_id, 'date' => $date));
                    if ($checkExists) {
                        $this->_shopReconcileInterface->updateById($checkExists->id, array(
                            'total_cod'         => $total_cod,
                            'money_indemnify'   => $money_indemnify,
                            'fee_transport'     => $fee_transport,
                            'fee_pick'          => $fee_pick,
                            'fee_forward'       => $fee_forward,
                            'fee_insurance'     => $fee_insurance,
                            'fee_cod'           => $fee_cod,
                            'fee_refund'        => $fee_refund,
                            'fee_store'         => $fee_store,
                            'fee_change_info'   => $fee_change_info,
                            'fee_transfer'      => $fee_transfer,
                            'refund_cod'        => $refund_cod,
                            'refund_transport'  => $refund_transport,
                        ));
                    } else {
                        $this->_shopReconcileInterface->create(array(
                            'shop_id'           => $shop_id,
                            'date'              => $date,
                            'total_cod'         => $total_cod,
                            'money_indemnify'   => $money_indemnify,
                            'fee_transport'     => $fee_transport,
                            'fee_pick'          => $fee_pick,
                            'fee_forward'       => $fee_forward,
                            'fee_insurance'     => $fee_insurance,
                            'fee_cod'           => $fee_cod,
                            'fee_refund'        => $fee_refund,
                            'fee_store'         => $fee_store,
                            'fee_change_info'   => $fee_change_info,
                            'fee_transfer'      => $fee_transfer,
                            'refund_cod'        => $refund_cod,
                            'refund_transport'  => $refund_transport,
                        ));
                    }

                }
            }

            DB::commit();
            $this->info(json_encode($arrDataCash));
            //
        } catch (\Throwable $e) {
            DB::rollBack();

            $message = $e->getMessage();
            $this->info($message);
        }

        $this->info('==============END=============');
        return;
    }
}
