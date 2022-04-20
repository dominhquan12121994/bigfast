<?php

/**
 * Class OrdersObserver
 * @package App\Modules\Orders\Models\Observer
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Orders\Models\Observers;

use App\Modules\Orders\Models\Entities\Orders;

class OrdersObserver
{
    /**
     * Handle the account "creating" event.
     * @param Orders $model
     * @return void
     */
    public function creating(Orders $model){

    }

    /**
     * Handle the account "created" event.
     * @param Orders $model
     * @return void
     */
    public function created(Orders $model)
    {
        //
    }

    /**
     * Handle the account "updated" event.
     *
     * @param Orders $model
     * @return void
     */
    public function updated(Orders $model)
    {
        //
    }

    /**
     * Handle the account "updating" event.
     *
     * @param Orders $model
     * @return void
     */
    public function updating(Orders $model)
    {
        //
    }

    /**
     * Handle the account "deleted" event.
     *
     * @param Orders $model
     * @return void
     */
    public function deleted(Orders $model)
    {
        //
    }

    /**
     * Handle the account "deleting" event.
     *
     * @param Orders $model
     * @return void
     */
    public function deleting(Orders $model)
    {
        //
    }

    /**
     * Handle the account "restored" event.
     *
     * @param Orders $model
     * @return void
     */
    public function restored(Orders $model)
    {
        //
    }

    /**
     * Handle the account "force deleted" event.
     *
     * @param Orders $model
     * @return void
     */
    public function forceDeleted(Orders $model)
    {
        //
    }
}
