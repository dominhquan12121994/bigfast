<?php

/**
 * Class ReportsObserver
 * @package App\Modules\Reports\Models\Observer
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Reports\Models\Observers;

use App\Modules\Reports\Models\Entities\Reports;

class ReportsObserver
{
    /**
     * Handle the account "creating" event.
     * @param Reports $model
     * @return void
     */
    public function creating(Reports $model){

    }

    /**
     * Handle the account "created" event.
     * @param Reports $model
     * @return void
     */
    public function created(Reports $model)
    {
        //
    }

    /**
     * Handle the account "updated" event.
     *
     * @param Reports $model
     * @return void
     */
    public function updated(Reports $model)
    {
        //
    }

    /**
     * Handle the account "updating" event.
     *
     * @param Reports $model
     * @return void
     */
    public function updating(Reports $model)
    {
        //
    }

    /**
     * Handle the account "deleted" event.
     *
     * @param Reports $model
     * @return void
     */
    public function deleted(Reports $model)
    {
        //
    }

    /**
     * Handle the account "deleting" event.
     *
     * @param Reports $model
     * @return void
     */
    public function deleting(Reports $model)
    {
        //
    }

    /**
     * Handle the account "restored" event.
     *
     * @param Reports $model
     * @return void
     */
    public function restored(Reports $model)
    {
        //
    }

    /**
     * Handle the account "force deleted" event.
     *
     * @param Reports $model
     * @return void
     */
    public function forceDeleted(Reports $model)
    {
        //
    }
}
