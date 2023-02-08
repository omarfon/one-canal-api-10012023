<?php

namespace App\Http\Requests\Auth;

use App\Constants\User;
use App\Helpers\Functions;
use Illuminate\Validation\Rule;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateAdminRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'email' => 'correo electrónico'
        ];
    }

    public function rules()
    {
        return [
            'email' => 'required|email'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
