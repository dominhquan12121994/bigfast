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

class CountStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:count-status {month?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count Status';

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
        return;
    }
}