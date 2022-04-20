<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Entities;

use Spatie\MediaLibrary\Models\Media as BaseMedia;

class Media extends BaseMedia
{

    protected $table = 'system_media';
    /**
     * Boot events
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($media) {
            if ($user = auth()->getUser()) {
                $media->user_id = $user->id;
            }
        });
    }

    /**
     * User relationship (one-to-one)
     * @return App\User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}