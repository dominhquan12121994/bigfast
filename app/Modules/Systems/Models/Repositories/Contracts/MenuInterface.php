<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Repositories\Contracts;

interface MenuInterface
{
    public function get(string $roles);
}