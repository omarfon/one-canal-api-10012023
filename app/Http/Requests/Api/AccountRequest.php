<?php

namespace App\Http\Requests\Api;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AccountRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'number' => 'número de cuenta',
            'bank_id' => 'banco',
            'cci' => 'cuenta interbancaria',
        ];
    }

    public function rules()
    {
        $rules = [
            'number' => 'required|string',
            'bank_id' => 'required|exists:banks,id',
            'cci' => 'required|string'
        ];

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
