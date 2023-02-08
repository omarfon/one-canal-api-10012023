<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangeStatusSelectedRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'ids' => 'seleccionados',
            'status' => 'nuevo estado'
        ];
    }

    public function rules()
    {
        $rules = [
            'ids' => 'required|array|min:1',
            'status' => 'required'
        ];

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
