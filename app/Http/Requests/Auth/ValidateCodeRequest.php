<?php

namespace App\Http\Requests\Auth;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateCodeRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'code' => 'código de recuperación',
            'email' => 'correo electrónico',
            'user_id' => 'identificador de usuario'
        ];
    }

    public function rules()
    {
        return [
            'code' => 'required',
            'email' => 'required|string',
            'user_id' => 'required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
