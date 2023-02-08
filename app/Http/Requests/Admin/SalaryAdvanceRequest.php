<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SalaryAdvanceRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'status' => 'estado'
        ];
    }

    public function rules()
    {
        $rules = [
            'status' => 'required|string'
        ];

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
