<?php

namespace App\Http\Middleware;

use DB;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Modules\Systems\Models\Entities\Menulist;
use App\Modules\Systems\Models\Entities\RoleHierarchy;
use App\Modules\Systems\Models\Repositories\Eloquent\GetSidebarMenu;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationInterface;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;
use App\Http\Controllers\Admin\AbstractAdminController;
use Validator;
use App\Modules\Systems\Services\NotificationServices;

class GetNotification extends AbstractAdminController
{
    protected $_notificationInterface;
    protected $_notificationSendInterface;
    protected $_notificationServices;

    public function __construct(NotificationInterface $notificationInterface,
                                NotificationServices $notificationServices,
                                NotificationSendInterface $notificationSendInterface)
    {
        parent::__construct();

        $this->_notificationInterface = $notificationInterface;
        $this->_notificationSendInterface = $notificationSendInterface;
        $this->_notificationServices = $notificationServices;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $shopId = 0;
        if (Auth::guard('shop')->check()) {
            $shopId = Auth::guard('shop')->id();
        }
        if (Auth::guard('shopStaff')->check()) {
            $shopId = 0;
        }

        $fiveNotification = $this->_notificationSendInterface->getMore(
            array(
                'shop_id' => $shopId,
                'is_read' => 0,
            ),
            array(
                'with' => array('notification'),
                'orderBy' => array(
                    'id',
                    ),
                'direction' => array(
                    'DESC'
                    ),
            ), 5
        );

        $fiveNotification->each(function ($item) {
            return $item->notification->content = $this->_notificationServices->generateContent(array(
                'content_data' => json_decode($item->notification->content_data)
            ));
        });

        $unreadNotification = $this->_notificationSendInterface->checkExist(array(
            'shop_id' => $shopId,
            'is_read' => false
        ));
        if (!$unreadNotification) {
            $unreadNotification = 0;
        }

        $notifications = array(
            'unread_notification' => $unreadNotification,
            'five_notifications' => $fiveNotification,
        );

        view()->share('notifications', $notifications);
        return $next($request);
    }
}
