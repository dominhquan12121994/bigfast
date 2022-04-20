<?php
/**
 * Define route for module
 * @author Electric <huydien.it@gmail.com>
 */

use App\Modules\Operators\Models\Entities\ZoneProvinces;
use App\Modules\Operators\Models\Entities\ZoneDistricts;
use App\Modules\Operators\Models\Entities\ZoneWards;

Route::group(['middleware' => ['get.menu', 'auth:admin']], function () {
    Route::resource('wards',        'WardsController', array('as' => 'admin'));
    Route::resource('districts',    'DistrictsController', array('as' => 'admin'));
    Route::resource('provinces',    'ProvincesController', array('as' => 'admin'));
    Route::resource('post-offices', 'PostOfficesController', array('as' => 'admin'));

    Route::prefix('contacts')->group(function () {
        Route::get('history/{id?}',                 'ContactsController@contactsHistory')->name('admin.contacts-history');
        Route::get('/download/{id?}/{position?}',   'ContactsController@getDownload')->name('admin.contacts.getDownload');
        Route::post('checkAllContacts',             'ContactsController@checkAllContacts')->name('admin.contacts.checkAllContacts');
    });

    Route::resource('contacts',         'ContactsController', array('as' => 'admin'));
    Route::resource('contacts-type',    'ContactsTypeController', array('as' => 'admin'));

    Route::get('print-templates',           'PrintTemplatesController@index')->name('admin.print-templates.index');
    Route::post('update-print-templates',   'PrintTemplatesController@update')->name('admin.print-templates.update');
    Route::post('print-preview',            'PrintTemplatesController@preview')->name('admin.admin.print-templates.preview');

    Route::get('print-orders', 'PrintTemplatesController@print')->name('admin.print-orders.print');

    Route::get('/el-provinces', function () {
//        ZoneProvinces::reindex();
//        ZoneProvinces::createIndex($shards = null, $replicas = null);
//        ZoneProvinces::putMapping($ignoreConflicts = true);
        ZoneProvinces::addAllToIndex();
        return 'Done!';
    });
    Route::get('/el-districts', function () {
//        ZoneDistricts::putMapping($ignoreConflicts = true);
//        ZoneDistricts::reindex();
//        ZoneDistricts::createIndex($shards = null, $replicas = null);
//        ZoneDistricts::putMapping($ignoreConflicts = true);
        ZoneDistricts::addAllToIndex();
        return 'Done!';
    });
    Route::get('/el-wards', function () {
//        ZoneWards::reindex();
//        ZoneWards::createIndex($shards = null, $replicas = null);
//        ZoneWards::putMapping($ignoreConflicts = true);
        ZoneWards::addAllToIndex();
        return 'Done!';
    });
});
