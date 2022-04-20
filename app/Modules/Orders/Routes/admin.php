<?php
/**
 * Define route for module
 * @author Electric <huydien.it@gmail.com>
 */

Route::group(['middleware' => ['get.menu', 'auth:admin']], function () {
    Route::prefix('orders')->group(function () {
        Route::post('imports/{shop_id?}',   'OrdersController@import')->name('admin.orders.import');
        Route::post('search',               'OrdersController@search')->name('admin.orders.search');
        Route::get('exports',               'OrdersController@export')->name('admin.orders.export');
        Route::get('download',              'OrdersController@download')->name('admin.orders.download');
        Route::get('create/{shop_id}',      'OrdersController@create')->name('admin.orders.create');
        Route::get('drafts/{shop_id?}',     'OrdersController@drafts')->name('admin.orders.drafts');
    });

    Route::resource('shops',        'ShopsController', array('as' => 'admin'));
    Route::resource('shop-staff',   'ShopStaffController', array('as' => 'admin'))->except(['index']);
    Route::resource('orders',       'OrdersController', array('as' => 'admin'))->except(['create']);
    Route::resource('order-incurred-fee',    'OrderFeeController', array('as' => 'admin'));
    Route::get('shop-staffs/{shop_id}',      'ShopStaffController@index')->name('admin.shop-staff.index');

    Route::prefix('order-setting')->group(function () {
        Route::get('',                  'OrderSettingController@index')->name('admin.order-setting.index');
        Route::get('edit',              'OrderSettingController@edit')->name('admin.order-setting.edit');
        Route::post('update',           'OrderSettingController@update')->name('admin.order-setting.update');
        Route::resource('cod',          'OrderCodController', array('as' => 'admin'));
        Route::resource('fee-pick',     'OrderFeePickController', array('as' => 'admin'));
        Route::resource('fee-insurance', 'OrderFeeInsuranceController', array('as' => 'admin'));
    });
    Route::resource('order-service', 'OrderServiceController', array('as' => 'admin'));

    Route::get('assign-ship',           'AssignShipController@show')->name('admin.assign-ship.show');
    Route::get('scan-barcode',          'AssignShipController@scanBarcode')->name('admin.assign-ship.scan-barcode');
    Route::get('calculator-fee',        'CalculatorFeeController@index');
    Route::get('calculator-fee-finish', 'CalculatorFeeController@calculator')->name('admin.calculator-fee-finish');
    Route::get('cash-flow',             'CashFlowController@index')->name('admin.reports.cash-flow');
    Route::get('cash-flow-export',      'CashFlowController@export')->name('admin.reports.cash-flow-export');
    Route::get('reconcile-history',     'ShopReconcileController@getReconcile')->name('admin.reports.reconcile-history');
    Route::get('reconcile-history-export', 'ShopReconcileController@export')->name('admin.reports.reconcile-history-export');

    Route::prefix('report')->group(function () {
        Route::get('by-zone',   'ReportsController@byZone')->name('admin.report.by-zone');
        Route::get('by-ship',   'ReportsController@byShip')->name('admin.report.by-ship');
        Route::get('by-status', 'ReportsController@byStatus')->name('admin.report.by-status');
    });

    Route::resource('user-notification', 'UserNotificationController', array('as' => 'admin'));
    Route::get('user-notification-read', 'UserNotificationController@setRead', array('as' => 'admin'))->name('admin.user-notification-read.index');

});
