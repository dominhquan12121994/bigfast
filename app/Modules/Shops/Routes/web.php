<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Define route for module
 * @author Electric <huydien.it@gmail.com>
 */

//Route::get('/', 'DashboardController@homepage')->middleware('checklogin');

//Auth::routes();
// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('shop.login');
Route::post('login', 'Auth\LoginController@login')->name('shop.login.post');
Route::post('logout', 'Auth\LoginController@logout')->name('shop.logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('shop.register');
Route::post('register', 'Auth\RegisterController@register')->name('shop.register.post');

// Password Reset Routes...
Route::get('password/reset',            'Auth\ForgotPasswordController@showLinkRequestForm')->name('shop.password.reset');
Route::post('password/email',           'Auth\ForgotPasswordController@sendResetLinkEmail')->name('shop.password.email');
Route::get('password/reset/{token}',    'Auth\ResetPasswordController@showResetForm')->name('shop.password.reset.token');
Route::post('password/reset',           'Auth\ResetPasswordController@reset')->name('shop.password.reset.post');

Route::any('/', function(){
    if (Auth::guard('shop')->check()) {
        return redirect()->route('shop.orders.index');
    } elseif (Auth::guard('shopStaff')->check()) {
        return redirect()->route('shop.order-staff.index');
    } else {
        abort(401);
    }
})->middleware('checkShopLogin');

Route::group(['middleware' => ['get.shop.menu', 'checkShopLogin', 'get.notification']], function () {
    Route::put('account-update', 'ShopsController@update')->name('shop.profile.update');
    Route::get('account', 'ShopsController@edit')->name('shop.profile.edit');
    Route::get('change-password',    'Auth\ChangePasswordController@changePassword')->name('shop.change.password');
    Route::put('update-password',    'Auth\ChangePasswordController@updatePassword')->name('shop.update.password');

    Route::group(['middleware' => ['checkShopInfo']], function () {
        Route::prefix('orders')->group(function () {
            Route::get('/', 'OrdersController@index')->name('shop.orders.index');
            Route::get('exports', 'OrdersController@export')->name('shop.orders.export');
            Route::get('create-by-shop', 'OrdersController@create')->name('shop.orders.create-by-shop');
            Route::post('imports', 'OrdersController@import')->name('shop.orders.import');
            Route::post('search', 'OrdersController@search')->name('shop.orders.search');
            Route::get('download', 'OrdersController@download')->name('shop.orders.download');
        });

        Route::resource('orders', 'OrdersController', array('as' => 'shop'));
        Route::get('order-drafts', 'OrdersController@drafts')->name('shop.orders-drafts');
       
        Route::prefix('order-staff')->group(function () {
            Route::get('/', 'OrderStaffController@index')->name('shop.order-staff.index');
            Route::get('exports', 'OrderStaffController@export')->name('shop.order-staff.export');
            Route::post('search', 'OrderStaffController@search')->name('shop.order-staff.search');
        });
        Route::resource('order-staff', 'OrderStaffController', array('as' => 'shop'));
        Route::resource('shop-staffs',   'ShopStaffController', array('as' => 'shop'));
        Route::resource('staff',   'StaffController', array('as' => 'shop'));

        Route::resource('shop-notification', 'ShopNotificationController', array('as' => 'shop'));
        Route::get('shop-notification-read', 'ShopNotificationController@saveReadNotification', array('as' => 'shop'))->name('shop.shop-notification-read');

        Route::get('cash-flow', 'CashFlowController@index')->name('shop.reports.cash-flow');
        Route::get('cash-flow-export', 'CashFlowController@export')->name('shop.reports.cash-flow-export');
        Route::get('reports', 'ReportsController@byStatus')->name('shop.reports');

        Route::prefix('contacts')->group(function () {
            Route::get('history/{id?}',                'ShopContactsController@contactsHistory')->name('shop.contacts-history');
            Route::get('/download/{id?}/{position?}',  'ShopContactsController@getDownload')->name('shop.contacts.getDownload');
            Route::post('checkAllContacts', 'ShopContactsController@checkAllContacts')->name('shop.contacts.checkAllContacts');
        });
        Route::resource('contacts', 'ShopContactsController', array('as' => 'shop'));

        Route::prefix('contacts-staff')->group(function () {
            Route::get('history/{id?}',                'ShopStaffContactsController@contactsHistory')->name('shop.contacts-staff-history');
            Route::get('/download/{id?}/{position?}',  'ShopStaffContactsController@getDownload')->name('shop.contacts-staff.getDownload');
            Route::post('checkAllContacts', 'ShopStaffContactsController@checkAllContacts')->name('shop.contacts-staff.checkAllContacts');
        });
        Route::resource('contacts-staff', 'ShopStaffContactsController', array('as' => 'shop'));

        Route::get('print-orders', 'PrintTemplatesController@print')->name('shop.print-orders.print');
        Route::get('reconcile-histories', 'ShopReconcileController@getReconcile')->name('shop.reconcile-histories');
    });
});
