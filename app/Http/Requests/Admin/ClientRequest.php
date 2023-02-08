<?php

namespace App\Http\Requests\Admin;

use App\Constants\User;
use App\Helpers\Functions;
use Illuminate\Validation\Rule;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'names' => 'nombre(s)',
            'surnames' => 'apellido(s)',
            'email' => 'correo electrónico',
            'document_type' => 'tipo de documento',
            'document_number' => 'número de documento',
            'business_id' => 'empresa',
            'salary' => 'salario',
            'marital_status_id' => 'estado civil',
            'address' => 'dirección',
            'business_job' => 'puesto de trabajo',
            'active' => 'estado'
        ];
    }

    public function rules()
    {
        $rules = [
            'names' => 'required|string',
            'surnames' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'document_type' => 'required|' . Rule::in(User::$document_type),
            'document_number' => 'required|string',
            'business_id' => 'required|exists:businesses,id',
            'salary' => 'nullable|sometimes|numeric',
            'marital_status_id' => 'sometimes|nullable|exists:marital_status,id',
            'address' => 'sometimes|nullable|string',
            'business_job' => 'sometimes|nullable|string',
            'active' => 'required|nullable|boolean'
        ];

        $document_type = request('document_type');

        if ($document_type && $document_type != 'null') {
            switch ($document_type) {
                case User::DNI:
                    $rules['document_number'] = 'required|digits:8';
                    break;
                case User::CE:
                case User::PAS:
                    $rules['document_number'] = 'required|alpha_num|min:1|max:12';
                    break;
                default:
                    break;
            }

            $document_number = request('document_number');

            $rules['document_number'] .= "|" . Rule::unique('users')->where(function ($query) use ($document_type, $document_number) {
                return $query->where('document_type', $document_type)
                    ->where('document_number', $document_number);
            })->ignore(request()->id);
        }

        if ($this->isMethod('PUT')) {
            $rules['email'] = 'unique:users,email,' . request()->id;
        }

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
