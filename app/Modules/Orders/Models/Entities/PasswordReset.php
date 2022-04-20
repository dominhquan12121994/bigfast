<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Models\Entities;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    protected $table = 'systems_password_resets';
    //
    protected $fillable = [
        'email',
        'token',
    ];
}
