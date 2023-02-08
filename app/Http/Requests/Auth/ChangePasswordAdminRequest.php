<?php

namespace App\Http\Requests\Auth;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordAdminRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'email' => 'correo electrónico',
            'password' => 'contraseña'
        ];
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|confirmed'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
