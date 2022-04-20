<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\AbstractAdminController;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Modules\Systems\Models\Repositories\Contracts\NotificationSendInterface;
use App\Modules\Systems\Services\NotificationServices;

class GetUserNotification extends AbstractAdminController
{
    protected $_notificationSendInterface;
    protected $_notificationServices;

    public function __construct(NotificationSendInterface $notificationSendInterface,
                                NotificationServices $notificationServices)
    {
        parent::__construct();

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
        if (Auth::guard('admin')->user()) {
            $userId = Auth::guard('admin')->id();
            $userRole = Auth::guard('admin')->user()->getRoleNames()[0];

            $isShipper = false;

            if (in_array($userRole, array('shipper', 'pickup'))) {
                $isShipper = true;
            }

            $fiveNotification = $this->_notificationSendInterface->getMore(
                array(
                    'user_id' => $userId,
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
                'user_id' => $userId,
                'is_read' => false
            ));

            if (!$unreadNotification) {
                $unreadNotification = 0;
            }

            $notifications = array(
                'unread_notification' => $unreadNotification,
                'five_notifications' => $fiveNotification,
            );

            view()->share([
                'notifications' => $notifications,
                'isShipper' => $isShipper,
            ]);
        }
        return $next($request);
    }
}
