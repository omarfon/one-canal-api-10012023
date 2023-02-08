<?php

namespace App\Http\Requests\Auth;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateTokenRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'access_token' => 'token de recuperación'
        ];
    }

    public function rules()
    {
        return [
            'access_token' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
