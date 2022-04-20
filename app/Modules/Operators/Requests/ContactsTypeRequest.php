<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Requests;

use App\Http\Requests\AbstractRequest;

class ContactsTypeRequest extends AbstractRequest
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = \Request::instance()->id;
    }
    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'Tên loại trợ giúp',
            'sla' => 'Sla',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->id) {
            return [
                'name' => 'required|max:255|unique:contacts_type,name,'.$this->id.',id,deleted_at,NULL',
                'sla' => 'max:10000',
            ];
        }
        return [
            'name' => 'required|max:255|unique:contacts_type,name,NULL,id,deleted_at,NULL',
            'sla' => 'max:10000',
        ];
        
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required'  => ':attribute là bắt buộc!',
            'max'       => ':attribute không được vượt quá :max ký tự!',
            'unique'    => ':attribute đã trùng',
            'numeric'   => ':attribute phải là số',
        ];
    }
}