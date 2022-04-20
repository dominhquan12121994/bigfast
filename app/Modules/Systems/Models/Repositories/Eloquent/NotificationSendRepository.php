<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Repositories\Eloquent;

use App\Models\Repositories\Eloquent\AbstractEloquentRepository;
use App\Modules\Systems\Models\Entities\NotificationSend;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;

/**
 * Class ShopBankRepository
 * @package App\Modules\Orders\Models\Repositories\Eloquent
 * @author HuyDien <huydien.it@gmail.com>
 */
class NotificationSendRepository extends AbstractEloquentRepository implements NotificationSendInterface
{
    /**
     * @return mixed|string
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _getModel()
    {
        return NotificationSend::class;
    }

    /**
     * @param $conditions
     * @param $query
     * @return mixed
     * @author HuyDien <huydien.it@gmail.com>
     */
    protected function _prepareConditions($conditions, $query)
    {
        $query = parent::_prepareConditions($conditions, $query);

        if (isset($conditions['shop_id'])) {
            $shop_id = $conditions['shop_id'];
            $query->where('shop_id', $shop_id);
        }

        if (isset($conditions['user_id'])) {
            $user_id = $conditions['user_id'];
            $query->where('user_id', $user_id);
        }

        if (isset($conditions['notification_id'])) {
            $notification_id = $conditions['notification_id'];
            $query->where('notification_id', $notification_id);
        }

        if (isset($conditions['is_read'])) {
            $is_read = $conditions['is_read'];
            $query->where('is_read', $is_read);
        }

        if (isset($conditions['arr_notification'])) {
            $arr_notification = $conditions['arr_notification'];
            $query->whereIn('id', $arr_notification);
        }

        if (isset($conditions['arr_notification_id'])) {
            $arr_notification = $conditions['arr_notification_id'];
            $query->whereIn('notification_id', $arr_notification);
        }

        if (isset($conditions['date_range'])) {
            $date_range = $conditions['date_range'];
            $query->whereBetween('created_date', $date_range);
        }

        if (isset($conditions['is_send'])) {
            $is_send = $conditions['is_send'];
            $query->where('is_send', $is_send);
        }

        return $query;
    }
}
