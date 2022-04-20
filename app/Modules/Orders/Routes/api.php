<?php
/**
 * Define route for module
 * @author Electric <huydien.it@gmail.com>
 */

Route::group([
    'prefix' => 'shop'
], function () {
    Route::post('login', 'ShopAuthController@login');
    Route::post('register', 'ShopAuthController@register');
    Route::post('reset-password', 'ShopAuthController@sendMail');
    Route::get('reset-password/{token}', 'ShopAuthController@reset');

    Route::group([
        'middleware' => ['auth:shop-api', 'scopes:shop']
    ], function() {
        Route::get('logout', 'ShopAuthController@logout');
        Route::get('get', 'ShopAuthController@get');
    });

    Route::group([
        'middleware' => [/*'role:admin'*/]
    ], function() {
        Route::get('find-by-name', 'ShopsController@findByName')->name('api.shops.find-by-name');
        Route::get('list-json', 'ShopsController@getListJson')->name('api.shops.list-json');
    });
});

// api/dashboard
Route::group([
    'prefix' => 'shipper',
    'middleware' => ['auth:admin-api', 'scopes:admin']
], function() {
    Route::get('dashboard', 'ShipperController@dashboard')->name('api.shipper.dashboard');
});

Route::get('/test', function () {
    $SERVER_API_KEY = config('firebase.init.SERVER_API_KEY');
    $data = [
        "registration_ids" => array('e2cS5HsoS-CFdOvt8EBgI2:APA91bGUXVSohLLV9LyWADnJYeZ94DwSHzMToXpNhE2QzY0RiqMUacKf_N70aGlieMczcWWzlcScAJlgd_pFSJWL3mv6JP7CpFcQCkqh350FhCAtP5LGrOOBpFiGZ5qTjITHjQcmX0sC'),
        "data" => array(
            'id' => 1,
            'content' => 'electric test',
            'date' => date('d/m/Y H:i'),
        ),
        "notification" => array(
            'title' => 'Thông báo từ BigFast',
            'body' => 'electric test',
        )
    ];

    $dataString = json_encode($data);
    $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, config('firebase.init.CURLOPT_URL'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    $response = curl_exec($ch);
    dd($response);
});

Route::group([
    'prefix' => 'user',
    'middleware' => ['auth:admin-api', 'scopes:admin']
], function() {
    Route::put('set-notification-read',  'UserNotificationController@setRead')->name('api.set-user-notification-read');
});

// api/v1/order/create
Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:shop-token']
], function() {
    Route::group([
        'prefix' => 'order'
    ], function () {
        Route::post('create', 'OrderShopController@create');
        Route::post('cancel', 'OrderShopController@cancel');
    });
});

Route::group([
    'prefix' => 'order'
], function () {

    Route::get('get-by-lading-code', 'OrdersController@getByLadingCode')->name('api.orders.get-by-lading-code')->middleware(['cors']);

    Route::group([
        'middleware' => ['auth:admin-api', 'scopes:admin']
    ], function() {
        Route::get('get-modal-action', 'OrdersController@getModalAction')->name('api.orders.get-modal-action');
        Route::get('get-by-shipper', 'OrdersController@getByShipper')->name('api.orders.get-by-shipper');
        Route::get('search', 'OrdersController@search')->name('api.orders.search');

        Route::post('load-more', 'OrdersController@loadMore')->name('api.shop.orders.load-more');
        Route::post('create-by-draft', 'OrdersController@createByDraft')->name('api.orders.create-by-draft');
        Route::put('update-status-order', 'OrdersController@updateStatusOrder')->name('api.orders.update-status-order')->middleware(['checkTimeWork']);

        Route::get('calculator-fee', 'CalculatorFeeController@calculator')->name('api.orders.calculator-fee');
    });

    Route::group([
        'middleware' => ['auth:shop-api', 'scopes:shop']
    ], function() {
        Route::post('create-by-draft/{shop?}', 'OrdersController@createByDraft')->name('api.shop.orders.create-by-draft');
        Route::get('get-by-shop', 'OrderShopController@getByShop')->name('api.orders.get-by-shop');
        Route::post('store-by-shop', 'OrderShopController@store')->name('api.orders.store-by-shop');
        Route::get('calculator-fee-by-shop', 'CalculatorFeeController@calculatorByShop')->name('api.shop.orders.calculator-fee');
        Route::get('setting-by-shop', 'OrderShopController@settingByShop')->name('api.shop.orders.setting');
        Route::get('search-by-shop', 'OrderShopController@searchByShop')->name('api.orders.search-by-shop');
    });
});

Route::group([
    'prefix' => 'draft-order'
], function () {
    Route::group([
        'middleware' => ['auth:shop-api', 'scopes:shop']
    ], function() {
        Route::get('list', 'DraftOrdersController@list')->name('api.shop.draft-order.list');
        Route::post('create', 'DraftOrdersController@store')->name('api.shop.draft-order.create');
        Route::post('create-order', 'DraftOrdersController@createOrder')->name('api.shop.draft-order.create-order');
        Route::get('edit', 'DraftOrdersController@edit')->name('api.shop.draft-order.edit');
        Route::put('update', 'DraftOrdersController@update')->name('api.shop.draft-order.update');
        Route::delete('delete', 'DraftOrdersController@destroy')->name('api.shop.draft-order.delete');
    });
});

Route::group([
    'prefix' => 'order_service'
], function () {
    Route::group([
        'middleware' => ['auth:admin-api']
    ], function() {
        Route::post('find', 'OrderServiceController@find')->name('api.order_service.find');
        Route::post('store-order-cod', 'OrderServiceController@storeOrderCod')->name('api.store-order-cod');
        Route::post('find-order-cod', 'OrderServiceController@findOrderCod')->name('api.find-order-cod');
        Route::post('update-order-cod', 'OrderServiceController@updateOrderCod')->name('api.update-order-cod');
        Route::post('store-order-fee-pick', 'OrderServiceController@storeOrderFeePick')->name('api.store-order-fee-pick');
        Route::post('find-order-fee-pick', 'OrderServiceController@findOrderFeePick')->name('api.find-order-fee-pick');
        Route::post('update-order-fee-pick', 'OrderServiceController@updateOrderFeePick')->name('api.update-order-fee-pick');
        Route::post('store-order-fee-insurance', 'OrderServiceController@storeOrderFeeInsurance')->name('api.store-order-fee-insurance');
        Route::post('find-order-fee-insurance', 'OrderServiceController@findOrderFeeInsurance')->name('api.find-order-fee-insurance');
        Route::post('update-order-fee-insurance', 'OrderServiceController@updateOrderFeeInsurance')->name('api.update-order-fee-insurance');
    });
});

Route::group([
    'middleware' => ['auth:admin-api']
], function() {
    Route::post('shop-reconcile', 'ShopReconcileController@doReconcile')->name('api.shop-reconcile');
});
