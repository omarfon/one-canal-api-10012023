<?php

namespace App\Http\Requests\Api;

use App\Helpers\Functions;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SalaryAdvanceRequest extends FormRequest
{
    use ApiResponse;

    public function attributes()
    {
        return [
            'amount' => 'monto de adelanto',
            'fees' => 'comisiones totales',
            'transfer_amount' => 'monto a transferir',
            'account_id' => 'cuenta de abono',
            'period_name' => 'periodo de pago',
            'salary_advance_reason_id' => 'motivo del adelanto'
        ];
    }

    public function rules()
    {
        $user = Auth::user();

        return [
            'amount' => 'required|numeric',
            'fees' => 'required|string',
            'transfer_amount' => 'required|string',
            'account_id' => 'required|exists:accounts,id,user_id,' . $user->id,
            'period_name' => 'required|string',
            'reason_id' => 'required|exists:reasons,id'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorResponse(Functions::getValidatorMessage($validator), 422));
    }
}
