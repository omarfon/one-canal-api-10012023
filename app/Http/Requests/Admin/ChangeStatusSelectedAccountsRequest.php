<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangeStatusSelectedAccountsRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'accounts_id' => 'cuentas',
            'status' => 'nuevo estado'
        ];
    }

    public function rules()
    {
        $rules = [
            'accounts_id' => 'required|array',
            'accounts_id.*' => 'exists:accounts,id',
            'status' => 'required|boolean'
        ];

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
