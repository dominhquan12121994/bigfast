<?php
/**
 * Define route for module
 * @author Electric <huydien.it@gmail.com>
 */
use Illuminate\Support\Facades\Mail;
use App\Modules\Systems\Mail\ResetPasswordMail;

Route::get('test', function () {

    $objDemo = new \stdClass();
    $objDemo->demo_one = 'Demo One Value';
    $objDemo->demo_two = 'Demo Two Value';
    $objDemo->sender = 'SenderUserName';
    $objDemo->receiver = 'ReceiverUserName';

    Mail::to("huydien.it@gmail.com")->send(new ResetPasswordMail($objDemo));
});

Route::group([
    'prefix' => 'user'
], function () {
    Route::post('login', 'AuthController@login')->name('api.user.login');
    Route::post('register', 'AuthController@register');

    Route::group([
        'middleware' => [/*'role:admin'*/]
    ], function() {
        Route::get('find-by-roles', 'UsersController@findByRoles')->name('api.user.find-by-roles');
        Route::get('find-by-text', 'UsersController@findByText')->name('api.user.find-by-text');
        Route::get('find-by-permission', 'UsersController@findByPermission')->name('api.user.find-by-permission');
    });

    Route::group([
        'middleware' => ['auth:admin-api', 'scopes:admin']
    ], function() {
        Route::get('get', 'AuthController@get')->name('api.user.get');
        Route::get('logout', 'AuthController@logout');
        Route::get('join-work', 'AuthController@joinWork');
        Route::put('change-password', 'AuthController@updatePassword');
    });
});

Route::group([
    'prefix' => 'role',
    'middleware' => ['auth:admin-api', 'scopes:admin']
], function () {
    Route::put('permission', 'RolesController@permission')->name('api.role.permission');
});

Route::group([
    'prefix' => 'shipper'
], function () {
    Route::group([
        'middleware' => ['auth:admin-api', 'scopes:admin']
    ], function() {
        Route::get('settings', 'IndexController@settings');
        Route::post('call-history', 'IndexController@callHistory');
    });
});

Route::group([
    'prefix' => 'shipper'
], function () {
    Route::group([
        'prefix' => 'notification',
        'middleware' => ['auth:admin-api', 'scopes:admin'],
    ], function () {
        Route::get('list', 'NotificationController@getByShipper')->name('api.shipper-notification.list');
        Route::put('read', 'NotificationController@readByShipper')->name('api.shipper-notification.read');
        Route::delete('delete', 'NotificationController@deleteByShipper')->name('api.shipper-notification.delete');
    });
});

Route::group([
    'prefix' => 'shop'
], function () {
    Route::group([
        'prefix' => 'notification',
        'middleware' => ['auth:shop-api', 'scopes:shop']
    ], function () {
        Route::get('list', 'NotificationController@getByShop')->name('api.shop-notification.list');
        Route::put('read', 'NotificationController@readByShop')->name('api.shop-notification.read');
        Route::delete('delete', 'NotificationController@deleteByShop')->name('api.shop-notification.delete');
    });
});
