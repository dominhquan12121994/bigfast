<?php

/**
 * Class OperatorsObserver
 * @package App\Modules\Operators\Models\Observer
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Models\Observers;

use App\Modules\Operators\Models\Entities\Operators;

class OperatorsObserver
{
    /**
     * Handle the account "creating" event.
     * @param Operators $model
     * @return void
     */
    public function creating(Operators $model){

    }

    /**
     * Handle the account "created" event.
     * @param Operators $model
     * @return void
     */
    public function created(Operators $model)
    {
        //
    }

    /**
     * Handle the account "updated" event.
     *
     * @param Operators $model
     * @return void
     */
    public function updated(Operators $model)
    {
        //
    }

    /**
     * Handle the account "updating" event.
     *
     * @param Operators $model
     * @return void
     */
    public function updating(Operators $model)
    {
        //
    }

    /**
     * Handle the account "deleted" event.
     *
     * @param Operators $model
     * @return void
     */
    public function deleted(Operators $model)
    {
        //
    }

    /**
     * Handle the account "deleting" event.
     *
     * @param Operators $model
     * @return void
     */
    public function deleting(Operators $model)
    {
        //
    }

    /**
     * Handle the account "restored" event.
     *
     * @param Operators $model
     * @return void
     */
    public function restored(Operators $model)
    {
        //
    }

    /**
     * Handle the account "force deleted" event.
     *
     * @param Operators $model
     * @return void
     */
    public function forceDeleted(Operators $model)
    {
        //
    }
}
