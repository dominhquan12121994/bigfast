<?php
namespace App\Response;

use Illuminate\Support\ServiceProvider;

class ResponseProvider extends ServiceProvider
{
    public function register()
    {
        app()->make(Response::class);
    }
}
