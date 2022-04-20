<?php
/**
 * Define route for module
 * @author Electric <huydien.it@gmail.com>
 */

Route::any('/', function(){
    return redirect()->route('admin.orders.index');
})->middleware(['checkLogin']);

Auth::routes();
Route::group(['middleware' => ['get.menu', 'auth:admin']], function () {

    Route::resource('users',    'UsersController', array('as' => 'admin'))/*->except( ['create', 'store'] )*/;
    Route::get('change-password',    'Auth\ChangePasswordController@changePassword')->name('admin.change.password');
    Route::put('update-password',    'Auth\ChangePasswordController@updatePassword')->name('admin.update.password');
    Route::resource('roles',    'RolesController', array('as' => 'admin'));
    Route::resource('mail',     'MailController', array('as' => 'admin'));
    Route::get('prepareSend/{id}',      'MailController@prepareSend')->name('admin.prepareSend');
    Route::post('mailSend/{id}',        'MailController@send')->name('admin.mailSend');
    Route::get('/roles/move/move-up',   'RolesController@moveUp')->name('admin.roles.up');
    Route::get('/roles/move/move-down', 'RolesController@moveDown')->name('admin.roles.down');
    Route::resource('shop-notification',  'ShopNotificationController', array('as' => 'admin'));

    Route::prefix('menu/element')->group(function () {
        Route::get('/',             'MenuElementController@index')->name('admin.menu.index');
        Route::get('/move-up',      'MenuElementController@moveUp')->name('admin.menu.up');
        Route::get('/move-down',    'MenuElementController@moveDown')->name('admin.menu.down');
        Route::get('/create',       'MenuElementController@create')->name('admin.menu.create');
        Route::post('/store',       'MenuElementController@store')->name('admin.menu.store');
        Route::get('/get-parents',  'MenuElementController@getParents');
        Route::get('/edit',         'MenuElementController@edit')->name('admin.menu.edit');
        Route::post('/update',      'MenuElementController@update')->name('admin.menu.update');
        Route::get('/show',         'MenuElementController@show')->name('admin.menu.show');
        Route::get('/delete',       'MenuElementController@delete')->name('admin.menu.delete');
    });

    Route::prefix('menu/menu')->group(function () {
        Route::get('/',         'MenuController@index')->name('admin.menu.menu.index');
        Route::get('/create',   'MenuController@create')->name('admin.menu.menu.create');
        Route::post('/store',   'MenuController@store')->name('admin.menu.menu.store');
        Route::get('/edit',     'MenuController@edit')->name('admin.menu.menu.edit');
        Route::post('/update',  'MenuController@update')->name('admin.menu.menu.update');
        Route::get('/delete',   'MenuController@delete')->name('admin.menu.menu.delete');
    });

    Route::prefix('media')->group(function () {
        Route::get('/',                 'MediaController@index')->name('admin.media.folder.index');
        Route::get('/folder/store',     'MediaController@folderAdd')->name('admin.media.folder.add');
        Route::post('/folder/update',   'MediaController@folderUpdate')->name('admin.media.folder.update');
        Route::get('/folder',           'MediaController@folder')->name('admin.media.folder');
        Route::post('/folder/move',     'MediaController@folderMove')->name('admin.media.folder.move');
        Route::post('/folder/delete',   'MediaController@folderDelete')->name('admin.media.folder.delete');

        Route::post('/file/store',      'MediaController@fileAdd')->name('admin.media.file.add');
        Route::get('/file',             'MediaController@file', array('as' => 'admin'));
        Route::post('/file/delete',     'MediaController@fileDelete')->name('admin.media.file.delete');
        Route::post('/file/update',     'MediaController@fileUpdate')->name('admin.media.file.update');
        Route::post('/file/move',       'MediaController@fileMove')->name('admin.media.file.move');
        Route::post('/file/crop',       'MediaController@cropp', array('as' => 'admin'));
        Route::get('/file/copy',        'MediaController@fileCopy')->name('admin.media.file.copy');
    });

    Route::get('system-log','SystemLogController@index')->name('admin.system.log.index');
});
