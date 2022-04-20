<?php

/**
 * Class Operators
 * @package App\Modules\Operators\Models\Repositories\Eloquent
 * @author Electric <huydien.it@gmail.com>
 */

namespace App\Modules\Operators\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Operators\Models\Repositories\Contracts\ContactsInterface;

/* Model */
use App\Modules\Operators\Models\Entities\Contacts;

class ContactsRepository extends AbstractEloquentRepository implements ContactsInterface
{
    protected function _getModel()
    {
        return Contacts::class;
    }

    protected function _prepareConditions($conditions, $query)
    {
        if(isset($conditions['filter'])){
            $query->filter($conditions['filter']);
        }
        $query->select('contacts.*', 'contacts_type.sla')
        ->leftjoin('contacts_type', 'contacts.contacts_type_id', '=', 'contacts_type.id')
        ->whereNull('contacts_type.deleted_at');

        if(isset($conditions['id'])){
            if(is_array($conditions['id'])){
                $query->whereIn('contacts.id', $conditions['id']);
            }else {
                $query->where('contacts.id', (int)$conditions['id']);
            }
        }

        if (isset($conditions['lading_code'])) {
            $lading_code = $conditions['lading_code'];
            $query->where('lading_code', $lading_code);
        }

        if (isset($conditions['created_range'])) {
            $range = $conditions['created_range'];
            $query->whereBetween('created_date', $range);
        }

        if (isset($conditions['shop'])) {
            $shop = $conditions['shop'];
            $query->where('shop_id', (int)$shop);
        }

        if (isset($conditions['user_id'])) {
            $user_id = $conditions['user_id'];
            $query->where('user_id', (int)$user_id);
        }

        if (isset($conditions['assign_id'])) {
            $assign_id = $conditions['assign_id'];
            $query->where('assign_id', (int)$assign_id);
        }

        if (isset($conditions['contacts_type_id'])) {
            $contacts_type_id = $conditions['contacts_type_id'];
            $query->where('contacts_type_id', $contacts_type_id);
        }

        if (isset($conditions['status'])) {
            $status = $conditions['status'];
            $query->where('contacts.status', (int)$status);
        }

        if (isset($conditions['detail'])) {
            $detail = '%'.$conditions['detail'].'%';
            $query->where('detail', 'LIKE', $detail);
        }

        if (isset($conditions['expired_at'])) {
            $expired_at = $conditions['expired_at'];
            $query->where('expired_at', '<', $expired_at);
        }

        if (isset($conditions['null'])) {
            $query->whereNull($conditions['null']);
        }

        if (isset($conditions['not_null'])) {
            $query->whereNotNull($conditions['not_null']);
        }

        return $query;
    }

    protected function _prepareFetchOptions($fetchOptions, $query){
        $query = parent::_prepareFetchOptions($fetchOptions, $query);

        if(isset($fetchOptions['orderByMulti'])){
            $direction = isset($fetchOptions['directionMulti']) ? $fetchOptions['directionMulti'] : 'DESC';
            foreach ( $fetchOptions['orderByMulti'] as $key => $value) {
                $query->orderBy($value, $direction[$key]);
            }

        }

        return $query;
    }
}
