<?php
/**
 * Copyright (c) 2021. Electric
 */

/**
 * Define route for module
 * @author Electric <huydien.it@gmail.com>
 */

Route::group([
    'prefix' => 'shop'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::group(['middleware' => ['auth:shop-api', 'scopes:shop']], function() {
        Route::post('logout', 'AuthController@logout');
        Route::put('change-password', 'AuthController@updatePassword');
        Route::get('notification-list',  'ShopNotificationController@list')->name('api.shop.notification-list');
        Route::put('notification-read',  'ShopNotificationController@read')->name('api.shop.notification-read');
        Route::delete('notification-delete',  'ShopNotificationController@delete')->name('api.shop.notification-delete');
        Route::get('cash-flow', 'CashFlowController@index')->name('api.shop.cash-flow');
        Route::get('reconcile-history', 'CashFlowController@reconcileHistory')->name('api.shop.reconcile-history');
    });

    Route::group(['middleware' => ['auth:shop-api', 'scopes:shop']], function() {
        Route::get('dashboard', 'ShopController@dashboard')->name('api.shop.dashboard');
        Route::get('address-filter', 'ShopController@getFilterAddress')->name('api.shop.address-filter');
        Route::put('update-shop', 'ShopController@update')->name('api.shop.update');
    });

    Route::group(['middleware' => ['auth:shop-api', 'scopes:shop']], function() {
        Route::group(['prefix' => 'staffs'], function() {
            Route::get('list', 'ShopStaffController@index')->name('api.shop.staffs');
            Route::post('store', 'ShopStaffController@store')->name('api.shop.store-staffs');
            Route::put('update', 'ShopStaffController@update')->name('api.shop.update-staffs');
            Route::delete('delete', 'ShopStaffController@destroy')->name('api.shop.delete-staffs');
        });
    });

    Route::group(['middleware' => ['auth:shop-api', 'scopes:shop']], function() {
        Route::group(['prefix' => 'contacts'], function() {
            Route::get('list', 'ContactController@getList')->name('api.shop.contacts');
            Route::post('store', 'ContactController@store')->name('api.shop.contacts-store');
            Route::delete('delete', 'ContactController@destroy')->name('api.shop.contacts-delete');
        });
    });
    Route::get('contacts-download', 'ContactController@getDownload')->name('api.shop.contacts-download');
});

Route::group(['middleware' => ['auth:shop-api', 'scopes:shop']], function() {
    Route::put('update-notification-read',  'ShopNotificationController@updateNotificationRead')->name('api.update-notification-read');
});
