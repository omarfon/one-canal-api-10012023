<?php

namespace App\Http\Resources\Admin;

use App\Helpers\SalaryAdvanceHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class SalaryAdvanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'account_bank_name' => $this->account->bank->short_name ?? $this->account->bank->name,
            'account_number' => $this->account->number,
            'account_cci' => $this->account->cci,
            'period_name' => $this->period_name,
            'amount' => SalaryAdvanceHelper::addCurrency($this->amount),
            'transfer_amount' => SalaryAdvanceHelper::addCurrency($this->transfer_amount),
            'fees_amount' => SalaryAdvanceHelper::addCurrency($this->fees_amount),
            'reason' => $this->reason->name,
            'status' => $this->status,
            'logs' => json_decode($this->logs),
            'reason_name' => $this->reason->name,
            'user_document' => $this->user->document_type . " " . $this->user->document_number,
            'user_name' => $this->user->names . " " . $this->user->surnames,
            'business_ruc' => "RUC " . $this->user->business->ruc,
            'business_name' => $this->user->business->name,
            'date' => date("d-m-Y H:i:s", strtotime($this->created_at))
        ];
    }
}
