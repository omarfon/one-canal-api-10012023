<?php

namespace App\Http\Requests\Admin;

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
            'cci' => 'Código de Cuenta Interbancario',
            'bank_id' => 'banco',
            'user_id' => 'cliente',
            'active' => 'estado'
        ];
    }

    public function rules()
    {
        $rules = [
            'number' => 'required|string',
            'cci' => 'sometimes|nullable|string',
            'bank_id' => 'required|exists:banks,id',
            'user_id' => 'required|exists:users,id',
            'active' => 'required|boolean'
        ];

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
