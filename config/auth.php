<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => 'admin',
        'passwords' => 'users',
    ],

    'one_device' => true,

    'time_work' => [
        [
            'begin' => ' 08:30:00',
            'end' => ' 13:30:00'
        ],
        [
            'begin' => ' 13:30:00',
            'end' => ' 21:30:00'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'admin' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'admin-api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
        'shop' => [
            'driver' => 'session',
            'provider' => 'shops',
        ],
        'shop-api' => [
            'driver' => 'passport',
            'provider' => 'shops',
        ],
        'shop-token' => [
            'driver' => 'token',
            'provider' => 'shops',
        ],
        'shopStaff' => [
            'driver' => 'session',
            'provider' => 'shopStaff',
        ],
        'shopStaff-api' => [
            'driver' => 'passport',
            'provider' => 'shopStaff',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Modules\Systems\Models\Entities\User::class,
        ],
        'shops' => [
            'driver' => 'eloquent',
            'model' => App\Modules\Orders\Models\Entities\OrderShop::class,
        ],
        'shopStaff' => [
            'driver' => 'eloquent',
            'model' => App\Modules\Orders\Models\Entities\OrderShopStaff::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'systems_password_resets',
            'expire' => 60,
        ],
        'shops' => [
            'provider' => 'shops',
            'table' => 'systems_password_resets',
            'expire' => 60,
        ],
        'shopStaff' => [
            'provider' => 'shopStaff',
            'table' => 'systems_password_resets',
            'expire' => 60,
        ],
    ],

];
