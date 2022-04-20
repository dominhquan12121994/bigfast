<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Controllers\Web;

use App\Http\Controllers\Web\AbstractWebController;

class DashboardController extends AbstractWebController
{
    public function __construct()
    {

    }

    public function homepage()
    {
        return view('Systems::homepage');
    }
}
