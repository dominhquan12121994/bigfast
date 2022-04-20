<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Models\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use App\Helpers\FileUpload;
use App\Modules\Orders\Models\Entities\Orders;
use DateTime;

use App\Modules\Operators\Models\Repositories\Contracts\ContactsInterface;

class ContactsServices
{
    protected $_contactsInterface;

    public function __construct(ContactsInterface $contactsInterface)
    {
        $this->_contactsInterface = $contactsInterface;
    }

    public function handleUpload($files, $listFile = [])
    {
        foreach ( $files as $file ) {
            $upload = FileUpload::doUpload( $file, ['file_types' => 'contacts']);
            $listFile[] = $upload['file_path'];
        }
        $ary = implode(';', $listFile);

        return $ary;
    }

    public function convertMinToDay($min) {
        // $seconds = (int)$min * 60;
        // $dt1 = new DateTime("@0");
        // $dt2 = new DateTime("@$seconds");
        // $convert = $dt1->diff($dt2)->format('%a days, %h hours, %i minutes');
        $convert = '';
        if ($min != 0) {
            $convert .= $min . ' phút';
        }
        
        return $convert;
    }
}
