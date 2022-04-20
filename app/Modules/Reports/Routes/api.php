<?php
/**
 * Define route for module
 * @author Electric <huydien.it@gmail.com>
 */

Route::post('cod-report', 'ReportsController@codReport')->name('api.cod-report.index');

Route::prefix('report')->group(function () {
    Route::group([
        'middleware' => ['auth:admin-api']
    ], function() {
        Route::get('shipper', 'ReportsController@shipper')->name('api.report.shipper');
        Route::post('listShopByShip', 'ReportsController@listShopByShip')->name('api.report.listShopByShip');
    });
});