<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Systems\Models\Entities;

use App\Models\Entities\AbstractModel;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Folder extends AbstractModel implements HasMedia
{
    use HasMediaTrait;

    protected $table = 'system_folder';
    
    /**
     * Get the comments for the blog post.
     */
//    public function media()
//    {
//        return $this->hasMany('App\Modules\Systems\Models\Entities\Media');
//    }
}