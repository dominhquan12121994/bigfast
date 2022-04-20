<?php
/**
 * Copyright (c) 2021. Electric
 */

namespace App\Modules\Shops\Resources;

use Illuminate\Support\Collection;
use App\Http\Resources\AbstractResource;

use App\Modules\Operators\Constants\ContactsConstant;

/**
 * Class ContactResource
 * package App\Modules\Transport\Resources
 * author HuyDien <huydien.itgmail.com>
 */
class ContactResource extends AbstractResource
{
    /**
     * param $request
     * return array
     * author HuyDien <huydien.itgmail.com>
     */
    public function toArray($request)
    {
        if (isset($this->resource['data'])) {
            $this->resource['lists'] = $this->resource['data']->transform(function ($item) {
                return array(
                    'id' => $item->id,
                    'lading_code' => $item->lading_code,
                    'status' => ContactsConstant::status[$item->status],
                    'contact_type' => $item->typeContacts->name,
                    'detail' => $item->detail,
                    'assign_name' => $item->assign ? $item->assign->name : null,
                    'file_path' => $item->file_path ? (collect(explode(';', $item->file_path))->transform(function ($file , $key) use ($item) {
                        return array(
                            'name' => $file,
                            'url' => route('api.shop.contacts-download', ['id' => $item->id, 'position' => $key])
                        );
                    })) : [],
                    'created_at' => date('d/m/Y H:i', strtotime($item->created_at) ),
                    'history' => $item->history->transform(function ($item1) {
                        $jsonDetail = json_decode($item1->detail);
                        $item1->action = property_exists($jsonDetail, 'action') ? $jsonDetail->action : '';
                        $item1->column = property_exists($jsonDetail, 'column') ? $jsonDetail->column : '';
                        $item1->old = property_exists($jsonDetail, 'old') ? $jsonDetail->old : '';
                        $item1->new = property_exists($jsonDetail, 'new') ? $jsonDetail->new : '';

                        if ($item1->action == 'create') {
                            $text = 'Thêm mới trợ giúp';
                        } elseif ($item1->action == 'update') { 
                            if ($item1->column == 'assign_id') {
                                $text = 'Gán người trợ giúp cho ' .  $item1->new ;
                            } elseif ( $item1->column == 'file_path' ) {
                                $text = 'Đã thay đổi file tải lên';
                            } elseif ( $item1->column == 'status' ) {
                                if ($item1->new == 3) {
                                    $text =  $status[$item1->new]  . 'yêu cầu' ;
                                } else {
                                    $text =  $status[$item1->new]  . 'yêu cầu';
                                }
                            } else {
                                $text = 'Thay đổi trạng thái ' .  $item1->column  . ' từ '. $item1->old .' thành ' . $item1->new;
                            }
                        } else {
                            $text = 'Xóa trợ giúp';
                        }
                        return array(
                            'detail' => $text,
                            'assign_name' => $item1->type === 'shop' ? $item1->shop->name : $item1->shop->user, 
                            'created_at' => date('d/m/Y H:i' , strtotime($item1->created_at) ) 
                        );
                    })
                );
            });
        }
        

        $this->resource['filter']['status'] = collect(ContactsConstant::status)->transform(function ($item, $key) {
            return array(
                'status' => $key,
                'name' => $item
            );
        });

        unset($this->resource['data']);

        return $this->resource;
    }
}