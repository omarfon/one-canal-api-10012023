<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BusinessRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'ruc' => 'ruc',
            'name' => 'nombre',
            'active' => 'estado'
        ];
    }

    public function rules()
    {
        $rules = [
            'ruc' => 'required|string',
            'name' => 'required|string',
            'reliability' => 'required|numeric|min:0|max:100',
            'active' => 'required|boolean'
        ];

        if ($this->isMethod('PUT')) {
            $rules['name'] = 'unique:businesses,name,' . request()->id;
            $rules['ruc'] = 'unique:businesses,ruc,' . request()->id;
        }

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
