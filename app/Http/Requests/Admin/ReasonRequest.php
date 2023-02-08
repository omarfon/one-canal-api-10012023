<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReasonRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'name' => 'nombre',
            'active' => 'estado'
        ];
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string',
            'active' => 'required|boolean'
        ];

        if ($this->isMethod('PUT')) {
            $rules['name'] = 'unique:reasons,name,' . request()->id;
        }

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
