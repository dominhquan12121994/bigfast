<?php

/**
 * Class AppServiceProvider
 * @package App\Modules\Orders\Providers
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public function register()
    {
        // Register services
        foreach ($this->services as $interface => $repository) {
            $this->app->singleton($interface, $repository);
        }
    }

    protected $services = [
        'App\Modules\Orders\Models\Repositories\Contracts\ShopsInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\ShopsRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\ShopStaffInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\ShopStaffRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\ShopBankInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\ShopBankRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\ShopAddressInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\ShopAddressRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrdersInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrdersRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderFeeInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderFeeRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderFeeShopInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderFeeShopRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderLogInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderLogRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderExtraInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderExtraRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderQueueInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderQueueRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderTraceInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderTraceRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderProductInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderProductRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderReceiverInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderReceiverRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderServiceInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderServiceRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderSettingInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderSettingRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderSettingCodInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderSettingCodRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderShopReconcileInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderShopReconcileRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderShopTransferInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderShopTransferRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderShipAssignedInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderShipAssignedRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderSettingFeePickInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderSettingFeePickRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderSettingFeeInsuranceInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderSettingFeeInsuranceRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\ReportsByZoneInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\ReportsByZoneRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\ShopReconcileInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\ShopReconcileRepository',
        'App\Modules\Systems\Models\Repositories\Contracts\NotificationInterface' => 'App\Modules\Systems\Models\Repositories\Eloquent\NotificationRepository',
        'App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface' => 'App\Modules\Systems\Models\Repositories\Eloquent\NotificationSendRepository',
        'App\Modules\Orders\Models\Repositories\Contracts\OrderStatusOvertimeInterface' => 'App\Modules\Orders\Models\Repositories\Eloquent\OrderStatusOvertimeRepository',
    ];
}
