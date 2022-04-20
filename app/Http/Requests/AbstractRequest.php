<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

abstract class AbstractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function wantsJson()
    {
        if(Str::contains($this->getRequestUri(), '/api'))
            return true;
        return parent::wantsJson();
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        \Log::info($errors);
        throw new HttpResponseException(response()->json(
            [
                'message' => reset($errors)[0],
                'errors' => $this->_prettyErrors($errors),
                'status_code' => 422,
                'success' => false
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }

    protected function _prettyErrors($errors){
        return collect($errors)->map(function ($item, $key){
            return array(
                    'msg' => $item[0],
                    'param' => $key
                );
        });
    }
}
