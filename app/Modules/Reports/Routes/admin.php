<?php
/**
 * Define route for module
 * @author Electric <huydien.it@gmail.com>
 */
Route::group(['middleware' => ['get.menu', 'auth:admin']], function () {
    Route::get('report/cod-report', 'ReportsController@codReport')->name('admin.reports.cod-report');

    Route::prefix('report')->group(function () {
        Route::get('by-zone',   'ReportsController@byZone')->name('admin.report.by-zone');
        Route::get('by-ship',   'ReportsController@byShip')->name('admin.report.by-ship');
        Route::get('by-status', 'ReportsController@byStatus')->name('admin.report.by-status');
    });
});
