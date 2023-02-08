<?php

namespace App\Http\Requests\Api;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePasswordRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'current_password' => 'contraseña actual',
            'password' => 'nueva contraseña'
        ];
    }

    public function rules()
    {
        return [
            'current_password' => 'required|min:6',
            'password' => 'required|confirmed|min:6'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
