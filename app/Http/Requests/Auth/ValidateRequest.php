<?php

namespace App\Http\Requests\Auth;

use App\Constants\User;
use App\Helpers\Functions;
use Illuminate\Validation\Rule;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'document_type' => 'tipo de documento',
            'document_number' => 'número de documento'
        ];
    }

    public function rules()
    {
        return [
            'document_type' => 'required|' . Rule::in(User::$document_type),
            'document_number' => 'required|min:8'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
