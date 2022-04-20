<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['get.menu']], function () {
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/colors', function () {
            return view('components.colors');
        });
        Route::get('/typography', function () {
            return view('components.typography');
        });
        Route::get('/charts', function () {
            return view('components.charts');
        });
        Route::get('/widgets', function () {
            return view('components.widgets');
        });
        Route::get('/404', function () {
            return view('errors.404');
        });
        Route::get('/500', function () {
            return view('errors.500');
        });
        Route::prefix('base')->group(function () {
            Route::get('/breadcrumb', function () {
                return view('components.base.breadcrumb');
            });
            Route::get('/cards', function () {
                return view('components.base.cards');
            });
            Route::get('/carousel', function () {
                return view('components.base.carousel');
            });
            Route::get('/collapse', function () {
                return view('components.base.collapse');
            });

            Route::get('/forms', function () {
                return view('components.base.forms');
            });
            Route::get('/jumbotron', function () {
                return view('components.base.jumbotron');
            });
            Route::get('/list-group', function () {
                return view('components.base.list-group');
            });
            Route::get('/navs', function () {
                return view('components.base.navs');
            });

            Route::get('/pagination', function () {
                return view('components.base.pagination');
            });
            Route::get('/popovers', function () {
                return view('components.base.popovers');
            });
            Route::get('/progress', function () {
                return view('components.base.progress');
            });
            Route::get('/scrollspy', function () {
                return view('components.base.scrollspy');
            });

            Route::get('/switches', function () {
                return view('components.base.switches');
            });
            Route::get('/tables', function () {
                return view('components.base.tables');
            });
            Route::get('/tabs', function () {
                return view('components.base.tabs');
            });
            Route::get('/tooltips', function () {
                return view('components.base.tooltips');
            });
        });
        Route::prefix('buttons')->group(function () {
            Route::get('/buttons', function () {
                return view('components.buttons.buttons');
            });
            Route::get('/button-group', function () {
                return view('components.buttons.button-group');
            });
            Route::get('/dropdowns', function () {
                return view('components.buttons.dropdowns');
            });
            Route::get('/brand-buttons', function () {
                return view('components.buttons.brand-buttons');
            });
        });
        Route::prefix('icon')->group(function () {  // word: "icons" - not working as part of adress
            Route::get('/coreui-icons', function () {
                return view('components.icons.coreui-icons');
            });
            Route::get('/flags', function () {
                return view('components.icons.flags');
            });
            Route::get('/brands', function () {
                return view('components.icons.brands');
            });
        });
        Route::prefix('notifications')->group(function () {
            Route::get('/alerts', function () {
                return view('components.notifications.alerts');
            });
            Route::get('/badge', function () {
                return view('components.notifications.badge');
            });
            Route::get('/modals', function () {
                return view('components.notifications.modals');
            });
        });
    });
});