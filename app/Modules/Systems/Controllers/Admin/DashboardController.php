<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Systems\Controllers\Admin;

use App\Http\Controllers\Admin\AbstractAdminController;

class DashboardController extends AbstractAdminController
{
    public function __construct()
    {

    }

    public function homepage()
    {
        return view('Systems::homepage');
    }
}
