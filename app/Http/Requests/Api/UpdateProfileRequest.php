<?php

namespace App\Http\Requests\Api;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'address' => 'dirección',
            'marital_status_id' => 'estado civil',
            'business_job' => 'puesto en la empresa',
            'salary' => 'salario',
        ];
    }

    public function rules()
    {
        $rules = [
            'address' => 'required|string|max:250',
            'marital_status_id' => 'required|exists:marital_status,id',
            'business_job' => 'required|string',
            'salary' => 'nullable|sometimes|numeric|min:1',
        ];

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
