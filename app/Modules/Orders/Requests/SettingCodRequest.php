<?php
/**
 * Copyright (c) 2020. Electric
 */

namespace App\Modules\Orders\Requests;

use App\Http\Requests\AbstractRequest;

class SettingCodRequest extends AbstractRequest
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->max = \Request::instance()->max;
    }
    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'min' => 'Hạn mức thấp nhất',
            'max' => 'Hạn mức lớn nhất',
            'value' => 'COD',
            'type' => 'Loại COD',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $ruleMax = 'required|numeric';
        if ( $this->max != 0 ){
            $ruleMax = 'required|numeric|gt:min';
        }
        return [
            'min' => 'required|numeric',
            'max' => $ruleMax,
            'value' => 'required|numeric',
            'type' => 'required',
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
            'numeric' => ':attribute phải là số',
        ];
    }
}