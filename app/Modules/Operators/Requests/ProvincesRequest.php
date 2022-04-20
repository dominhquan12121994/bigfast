<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Operators\Requests;

use App\Http\Requests\AbstractRequest;

class ProvincesRequest extends AbstractRequest
{
    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'zone' => 'Khu vực',
            'code' => 'Mã tỉnh/thành',
            'name' => 'Tên tỉnh/thành'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'zone'             => 'required',
            'code'             => 'required|max:10',
            'name'             => 'required|max:255'
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
            'required' => ':attribute là bắt buộc!',
            'max' => ':attribute không được vượt quá :max ký tự!'
        ];
    }
}