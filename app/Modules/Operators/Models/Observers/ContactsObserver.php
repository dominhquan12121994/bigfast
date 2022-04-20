<?php

/**
 * Class ContactsObserver
 * @package App\Modules\Contacts\Models\Observer
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Models\Observers;

use App\Modules\Operators\Models\Entities\Contacts;
use App\Modules\Operators\Models\Entities\ContactsHistory;
use App\Modules\Operators\Models\Entities\ContactsType;
use Auth;

class ContactsObserver
{
    /**
     * Handle the account "creating" event.
     * @param Contacts $model
     * @return void
     */
    public function creating(Contacts $model){
        //
    }

    /**
     * Handle the account "created" event.
     * @param Contacts $model
     * @return void
     */
    public function created(Contacts $model)
    {
        $last_update = json_decode($model->last_update);
        $aryInsert = [
            'contacts_id'   => $model->id,
            'user_id'       => $last_update->id,
            'type'          => $last_update->type,
            'detail'        => json_encode( array( 'action' => 'create' ) ),
        ];
        
        ContactsHistory::create($aryInsert);
    }

    /**
     * Handle the account "updated" event.
     *
     * @param Contacts $model
     * @return void
     */
    public function updated(Contacts $model)
    {
        //fake $user vs TH call API
        $fakeUser = 1;

        $original = $model->getOriginal();
        $changes = $model->getChanges();
        $last_update = json_decode($model->last_update);
        $changes = [];

        foreach ( $model->getChanges() as $key => $value) {
            if ($key == 'updated_at' || $key == 'last_update') {
                continue;
            }
            if ($key == 'assign_id') {
                $value = $model->assign->name;
            }
            if ($key == 'contacts_type_id') {
                $value = ContactsType::find($value)->name;
                $original[$key] = ContactsType::find($original[$key])->name;
            }
            $changes[$key] = [
                'contacts_id'   => $model->id,
                'user_id'       => $last_update->id,
                'type'          => $last_update->type,
                'detail'        => json_encode( 
                    [
                        'action'    => 'update',
                        'column'    => $key,
                        'old'       => $original[$key],
                        'new'       => $value,
                    ]
                ),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        ContactsHistory::insert($changes);
    }

    /**
     * Handle the account "updating" event.
     *
     * @param Contacts $model
     * @return void
     */
    public function updating(Contacts $model)
    {
        //
    }

    /**
     * Handle the account "deleted" event.
     *
     * @param Contacts $model
     * @return void
     */
    public function deleted(Contacts $model)
    {
        $last_update = json_decode($model->last_update);
        $aryInsert = [
            'contacts_id'   => $model->id,
            'user_id'       => $last_update->id,
            'type'          => $last_update->type,
            'detail'        => json_encode( array( 'action' => 'delete' ) ),
        ];
        ContactsHistory::create($aryInsert);
    }

    /**
     * Handle the account "deleting" event.
     *
     * @param Contacts $model
     * @return void
     */
    public function deleting(Contacts $model)
    {
        //
    }

    /**
     * Handle the account "restored" event.
     *
     * @param Contacts $model
     * @return void
     */
    public function restored(Contacts $model)
    {
        //
    }

    /**
     * Handle the account "force deleted" event.
     *
     * @param Contacts $model
     * @return void
     */
    public function forceDeleted(Contacts $model)
    {
        //
    }
}
