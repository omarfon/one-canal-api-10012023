<?php

namespace App\Http\Requests\Auth;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidatePasswordRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'password' => 'contraseña',
            'access_token' => 'token de recuperación'
        ];
    }

    public function rules()
    {
        return [
            'password' => 'required|confirmed|min:6',
            'access_token' => 'required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
