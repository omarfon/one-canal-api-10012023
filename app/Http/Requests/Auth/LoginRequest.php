<?php

namespace App\Http\Requests\Auth;

use App\Constants\User;
use App\Helpers\Functions;
use Illuminate\Validation\Rule;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'document_type' => 'tipo de documento',
            'document_number' => 'número de documento',
            'password' => 'contraseña',
            'push' => 'device token'
        ];
    }
    
    public function rules()
    {
        return [
            'document_type' => 'required|' . Rule::in(User::$document_type),
            'document_number' => 'required|min:8',
            'password' => 'required|string|min:6',
            'push' => 'nullable|string'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
