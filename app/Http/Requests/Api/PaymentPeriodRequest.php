<?php

namespace App\Http\Requests\Api;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentPeriodRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'amount' => 'monto de adelanto'
        ];
    }

    public function rules()
    {
        return [
            'amount' => 'required|numeric'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
