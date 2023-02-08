<?php

namespace App\Http\Resources\Api;

use App\Helpers\SalaryAdvanceHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaryAdvanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'account' => new AccountResource($this->account),
            'period_name' => $this->period_name,
            'amount' => SalaryAdvanceHelper::addCurrency($this->amount),
            'transfer_amount' => SalaryAdvanceHelper::addCurrency($this->transfer_amount),
            'fees_amount' => SalaryAdvanceHelper::addCurrency($this->fees_amount),
            'reason' => new ReasonResource($this->reason),
            'status' => $this->status,
            'date' => date("d-m-Y", strtotime($this->created_at))
        ];
    }
}
