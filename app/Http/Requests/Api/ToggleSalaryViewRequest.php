<?php

namespace App\Http\Requests\Api;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ToggleSalaryViewRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'salary_view' => 'estado de vista de salario'
        ];
    }

    public function rules()
    {
        return [
            'salary_view' => 'required|boolean'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
