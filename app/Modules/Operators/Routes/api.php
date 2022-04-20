<?php
/**
 * Define route for module
 * @author Electric <huydien.it@gmail.com>
 */

Route::group(array('middleware' => array()), function () {
    Route::get('districts/get-by-province/{id}', 'DistrictsController@getByProvince')->name('api.districts.get-by-province');
    Route::get('wards/get-by-district/{id}', 'WardsController@getByDistrict')->name('api.wards.get-by-district');
    Route::get('post-offices/get-by-zone', 'PostOfficesController@getByZone')->name('api.post-offices.get-by-zone');

    Route::group([
        'middleware' => ['auth:admin-api']
    ], function() {
        Route::post('list-shop','ContactsController@listShop')->name('api.shop.list');
        Route::post('crud-contact-type', 'ContactsTypeController@storeContactsType')->name('api.contactType.create');
        Route::post('find-contact-type', 'ContactsTypeController@findContactType')->name('api.contactType.find');
        Route::post('update-contact-type', 'ContactsTypeController@updateContactType')->name('api.contactType.update');
        Route::post('change-status', 'ContactsController@changeStatus')->name('api.contact.changeStatus');
        Route::get('refuse-contact', 'ContactsController@refuseContact')->name('api.contact.refuse');
        Route::post('find', 'ContactsController@find')->name('api.contact.find');
    });

    Route::group([
        'middleware' => ['auth:admin-api,shop-api']
    ], function() {
        Route::post('find-shop','ContactsController@findShop')->name('api.shop.find');
    });
});
